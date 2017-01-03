<?php
/**
 *
 * The Authorization class used to authenticate the user provided credentials.
 *
 * @package Routee\lib\Api
 * @author  kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */

namespace Routee\lib\Api;

use Routee\Core as core;

use Routee\config as config; 

/**
 * Class Authorization
 *
 * An authorization transaction.
 *
 * @package Routee\Api
 *
 */

class Authorization 
{  
    /**
     * This is the default variable declaration for Routee Configuration
     *
     * @var string     
     */
    private $defaultRouteeConfig;

    /**
     * This method used to authenticate the User application credentials and return as Json data
     * @param array $param
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------ | ------------
     * grant_type | No | It must always be client_credentials.
     * scope | Yes | The body can also contain a scope parameter in order to limit the permissions of the access token. For example if an application only sends SMS it can request only the SMS scope. By default if the scope parameter is omitted then the token receives all the allowed scopes of the application.
     * @return string JSON
     * <code>
     * {
     *    "access_token":"string",
     *    "token_type":"string",
     *    "expires_in":"number",
     *    "scope":"string,string,string",
     *    "permissions":[
     *       "string"
     *    ]
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * access_token | The generated access_token. This must be used in all requests.
     * expires_in | Time in seconds that the token will expire. If for example it has 30, it means that the token will expire in 30 seconds. Default setting is for the token to be valid for 1 hour.
     * scope | The requested scopes.
     * permissions | The permissions granted to the authenticated application.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as auth;
     * $authResponse = new auth\Authorization();
     * $authResult = $authResponse->getAuthorization($config);
     * echo $authResult;
     * </code>
     * @throws RouteeConnectionException
     */
    
    public function getAuthorization($config = array())
    {     
        $defaultRouteeConfig = new config\RouteeConfig();

        $appConfig = $config;
        
        $this->defaultRouteeConfigUrls = (object) $defaultRouteeConfig->getDefaultUrl();
        $this->requestUrl = $this->defaultRouteeConfigUrls->authUrl;
        
        if(is_array($appConfig) && count($appConfig) == 0){
            $invalidResponse = array(
                'status'=>401 ,
                'message' => 'You should get your application credentials (application-id, application-secret) from the Routee Platform'
            );
            return json_encode($invalidResponse);           
        }
        

        $config = array();        
        
        $appId = (isset($appConfig['application-id'])) ?  $appConfig['application-id'] : '';
        $appSecret = (isset($appConfig['application-secret'])) ?  $appConfig['application-secret'] : '';
        $scopeData = (isset($appConfig['scope'])) ? $appConfig['scope'] : '';
                
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($appId.":".$appSecret)
        );

        $httpConfig = new core\RouteeHttpConfig(null, 'POST', $config);
        $httpConfig->setUrl($this->requestUrl);        
        $httpConfig->setHeaders( $headers);
        
        $connection = new core\RouteeHttpConnection($httpConfig, $config);
        $auth_data = array(
            'grant_type' => 'client_credentials',
            'scope'      => $scopeData
        );        
        
        $response = $connection->execute( http_build_query($auth_data));       
        
        return $response;
    }
}
