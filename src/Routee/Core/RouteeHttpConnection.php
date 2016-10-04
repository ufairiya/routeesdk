<?php
/**
  *
  * Http connection.
  *
  * @package Routee\Core
  * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
  *
  * @return void
  */

namespace Routee\Core;

use Routee\Exception as exceptions;

/**
 * A wrapper class based on the curl extension.
 * Requires the PHP curl module to be enabled.
 * See for full requirements the PHP manual: http://php.net/curl
 */

class RouteeHttpConnection
{

    /**
     * Private variable containing core\RouteeHttpConnection object
     * Used for making HTTP Requests and process Response.
     * @var RouteeHttpConfig
     */
    private $httpConfig;

    /**
     * HTTP status codes for which a retry must be attempted
     * retry is currently attempted for Request timeout, Bad Gateway,
     * Service Unavailable and Gateway timeout errors.
     */
    private static $retryCodes = array('408', '502', '503', '504',);
  

    /**
     * Default Constructor
     *
     * @param RouteeHttpConfig $httpConfig
     * @param array            $config
     * @throws RouteeConfigurationException
     */
    
    public function __construct(RouteeHttpConfig $httpConfig, array $config)
    {
        if (!function_exists("curl_init")) {
            throw new RouteeConfigurationException("Curl module is not available on this system");
        }
        $this->httpConfig = $httpConfig;
        
    }

    /**
     * Gets all Http Headers
     *
     * @return array
     */
    
    private function getHttpHeaders()
    {
        $ret = array();
        foreach ($this->httpConfig->getHeaders() as $k => $v) {
            $ret[] = "$k: $v";
        }
        return $ret;
    }

    /**
     * Executes an HTTP request
     *
     * @param string $data query string OR POST content as a string
     * @return mixed
     * @throws RouteeConnectionException
     */
    
    public function execute($data)
    {
        //print_R($data); exit;
        
        //Initialize Curl Options
        $ch = curl_init($this->httpConfig->getUrl());
        $options = $this->httpConfig->getCurlOptions();
        if (empty($options[CURLOPT_HTTPHEADER])) {
            unset($options[CURLOPT_HTTPHEADER]);
        }
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_URL, $this->httpConfig->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHttpHeaders()); 

        //Determine Curl Options based on Method
        switch ($this->httpConfig->getMethod()) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':           
            case 'PATCH':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
        }

        //Default Option if Method not of given types in switch case
        if ($this->httpConfig->getMethod() != null) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpConfig->getMethod());
        }

        //Logging Each Headers for debugging purposes
        foreach ($this->getHttpHeaders() as $header) {
            //TODO: Strip out credentials and other secure info when logging.
            // $this->logger->debug($header);
        }

        //Execute Curl Request
        $result = curl_exec($ch);

        //Retrieve Response Status
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Retry if Certificate Exception
        if (curl_errno($ch) == 60) {
            echo ("Invalid or no certificate authority found - Retrying using bundled CA certs file");
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            $result = curl_exec($ch);
            //Retrieve Response Status
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        //Retry if Failing
        $retries = 0;
        if (in_array($httpStatus, self::$retryCodes) && $this->httpConfig->getHttpRetryCount() != null) {
            echo ("Got $httpStatus response from server. Retrying");
            do {
                $result = curl_exec($ch);
                //Retrieve Response Status
                $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            } while (in_array($httpStatus, self::$retryCodes) && (++$retries < $this->httpConfig->getHttpRetryCount()));
        }
        //echo $this->httpConfig->getUrl(); exit;

        //Throw Exception if Retries and Certificates doenst work
        if (curl_errno($ch)) {
            $ex = new exceptions\RouteeConnectionException(
                $this->httpConfig->getUrl(),
                curl_error($ch),
                curl_errno($ch)
            );
            curl_close($ch);
            throw $ex;
        }

        if($this->httpConfig->getHttpResponse())
        {
            $response['response'] = curl_getinfo($ch);
            $response['result'] =json_decode($result);
            return json_encode($response);         
        }

        //Return result object
        return $result;
    }
}
