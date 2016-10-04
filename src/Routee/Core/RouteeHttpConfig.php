<?php
/**
 *
 * To declare the Curl connection configuration details here.
 *
 * @package Routee\Core
 * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */

namespace Routee\Core;

use Routee\Exception;

/**
 * Class RouteeHttpConfig
 * Http Configuration Class
 *
 * @package Routee\Core
 */

class RouteeHttpConfig
{
    /**
     * Some default options for curl
     * These are typically overridden by RouteeConnectionManager
     *
     * @var array
     */
    public static $defaultCurlOptions = array(
        CURLOPT_SSLVERSION => 6,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,      
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_TIMEOUT => 60,    // maximum number of seconds to allow cURL functions to execute
        CURLOPT_USERAGENT => 'Routee-PHP-SDK',
        CURLOPT_HTTPHEADER => array(),
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_CIPHER_LIST => 'TLSv1'
        //Allowing TLSv1 cipher list.
        //Adding it like this for backward compatibility with older versions of curl
    );

    const HEADER_SEPARATOR = ';';
    const HTTP_GET = 'GET';
    const HTTP_POST = 'POST';
    
    /**
     * This is the default variable declaration
     * 
     *
     * @var array
     */
    private $headers = array();

    /**
     * This is the default variable declaration
     * 
     *
     * @var string
     */

    private $curlOptions;

    /**
     * This is the default variable declaration
     * 
     *
     * @var string
     */

    private $url;

    /**
     * This is the default variable declaration
     * 
     *
     * @var string
     */

    private $method;

    /**
     * Number of times to retry a failed HTTP call
     *
     * @var number
     */
    private $retryCount = 0;

    /**
     * Default Constructor
     *
     * @param string $url
     * @param string $method HTTP method (GET, POST etc) defaults to POST
     * @param array $configs All Configurations
     */
    public function __construct($url = null, $method = self::HTTP_POST, $configs = array())
    {
        $this->url = $url;
        $this->method = $method;
        $this->httpResponse = FALSE;
        $this->curlOptions = $this->getHttpConstantsFromConfigs($configs, 'http.') + self::$defaultCurlOptions;
        // Update the Cipher List based on OpenSSL or NSS settings
        $curl = curl_version();
        $sslVersion = isset($curl['ssl_version']) ? $curl['ssl_version'] : '';
        if (substr_compare($sslVersion, "NSS/", 0, strlen("NSS/")) === 0) {
            //Remove the Cipher List for NSS
            $this->removeCurlOption(CURLOPT_SSL_CIPHER_LIST);
        }
    }

    /**
     * Gets Url
     *
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Gets Method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets Method
     *
     * @param string $method HTTP method (GET, POST etc) defaults to POST
     * @return string
     */
    public function setMethod( $method = '' )
    {
        $this->method = $method;
        return $this->method;
    }

    /**
     * Gets all Headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }



    /**
     * Get Header by Name
     *
     * @param $name
     * @return string|null
     */
    public function getHeader($name)
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        return null;
    }

    /**
     * Sets Url
     *
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Set Headers
     *
     * @param array $headers
     */
    public function setHeaders(array $headers = array())
    {
        $this->headers = $headers;
    }

    /**
     * Adds a Header
     *
     * @param      $name
     * @param      $value
     * @param bool $overWrite allows you to override header value
     */
    public function addHeader($name, $value, $overWrite = true)
    {
        if (!array_key_exists($name, $this->headers) || $overWrite) {
            $this->headers[$name] = $value;
        } else {
            $this->headers[$name] = $this->headers[$name] . self::HEADER_SEPARATOR . $value;
        }
    }

    /**
     * Removes a Header
     *
     * @param $name
     */
    public function removeHeader($name)
    {
        unset($this->headers[$name]);
    }

    /**
     * Gets all curl options
     *
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * Add Curl Option
     *
     * @param string $name
     * @param mixed  $value
     */
    public function addCurlOption($name, $value)
    {
        $this->curlOptions[$name] = $value;
    }

    /**
     * Removes a curl option from the list
     *
     * @param $name
     */
    public function removeCurlOption($name)
    {
        unset($this->curlOptions[$name]);
    }

    /**
     * Set Curl Options. Overrides all curl options
     *
     * @param $options
     */
    public function setCurlOptions($options)
    {
        $this->curlOptions = $options;
    }

    /**
     * Set ssl parameters for certificate based client authentication
     *
     * @param      $certPath
     * @param null $passPhrase
     */
    public function setSSLCert($certPath, $passPhrase = null)
    {
        $this->curlOptions[CURLOPT_SSLCERT] = realpath($certPath);
        if (isset($passPhrase) && trim($passPhrase) != "") {
            $this->curlOptions[CURLOPT_SSLCERTPASSWD] = $passPhrase;
        }
    }

    /**
     * Set connection timeout in seconds
     *
     * @param integer $timeout
     */
    public function setHttpTimeout($timeout)
    {
        $this->curlOptions[CURLOPT_CONNECTTIMEOUT] = $timeout;
    }

    /**
     * Set HTTP proxy information
     *
     * @param string $proxy
     * @throws RouteeConfigurationException
     */
    public function setHttpProxy($proxy)
    {
        $urlParts = parse_url($proxy);
        if ($urlParts == false || !array_key_exists("host", $urlParts)) {
            throw new RouteeConfigurationException("Invalid proxy configuration " . $proxy);
        }
        $this->curlOptions[CURLOPT_PROXY] = $urlParts["host"];
        if (isset($urlParts["port"])) {
            $this->curlOptions[CURLOPT_PROXY] .= ":" . $urlParts["port"];
        }
        if (isset($urlParts["user"])) {
            $this->curlOptions[CURLOPT_PROXYUSERPWD] = $urlParts["user"] . ":" . $urlParts["pass"];
        }
    }

    /**
     * Set Http Retry Counts
     *
     * @param int $retryCount
     */
    public function setHttpRetryCount($retryCount)
    {
        $this->retryCount = $retryCount;
    }

    /**
     * Set Http Response
     *
     * @param boolean $value
     */
    public function setHttpResponse($value = FALSE)
    {
        return $this->httpResponse = $value;
    }

    /**
     * Gets Http Response
     *
     * @return boolean
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * Get Http Retry Counts
     *
     * @return int
     */
    public function getHttpRetryCount()
    {
        return $this->retryCount;
    }

    /**
     * Sets the User-Agent string on the HTTP request
     *
     * @param string $userAgentString
     */
    public function setUserAgent($userAgentString)
    {
        $this->curlOptions[CURLOPT_USERAGENT] = $userAgentString;
    }

    /**
     * Retrieves an array of constant key, and value based on Prefix
     *
     * @param array $configs
     * @param       $prefix
     * @return array
     */
    public function getHttpConstantsFromConfigs($configs = array(), $prefix)
    {
        $arr = array();
        if ($prefix != null && is_array($configs)) {
            foreach ($configs as $k => $v) {
                // Check if it startsWith
                if (substr($k, 0, strlen($prefix)) === $prefix) {
                    $newKey = ltrim($k, $prefix);
                    if (defined($newKey)) {
                        $arr[constant($newKey)] = $v;
                    }
                }
            }
        }
        return $arr;
    }
}
