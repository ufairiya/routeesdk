<?php
/**
 *
 * 2Step verification API provides an easy way for your application to:
 *
 * verify the identity of the user by using his mobile phone.
 * validate the mobile phone of the user
 *
 * @package Routee\lib\Api
 * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */

namespace Routee\lib\Api;

use Routee\Core as core;

use Routee\lib\Api as auth;

use Routee\Exception as exceptions;

/**
 * Class TwoStep
 *
 *
 * @package Routee\lib\Api
 *
 */

class TwoStep
{
	/**
	 * This is the default variable declaration for access token
	 *
	 * @var string
	 */
	private $accessToken;

    /**
	 * This is the default variable declaration response from authentication
	 *
	 * @var string
	 */
	private $returnResponse;
	
    /**
	 * Private variable having configuration data.
	 * @var string
	 */
	private $config;
		
    /**
 	 * Private variable containing core\RouteeHttpConfig object
	 * Object used to set Headers before every request.
	 * @var core\RouteeHttpConfig object
	 */
	private $httpConfigObj;
	
    /**
	 * Private variable containing core\RouteeHttpConnection object
	 * Used for making HTTP Requests and process Response.
	 * @var core\RouteeHttpConnection object
	 */
	private $httpConnObj;    

    /**
	 * Default constructor.
	 * @param array $config
	 *  
	 */

    function __construct($config = array())
	{
		$this->config = $config;
		$this->httpMethod = 'GET';
		try
        {
            $authentication = new auth\Authorization();
            $authResponse   = $authentication->getAuthorization($this->config);
            $authDecodeResponse   = json_decode($authResponse);
            $this->returnResponse = $authResponse;

            $this->twoStepVerifyUrl = $authentication->defaultRouteeConfigUrls->twoStepVerifyUrl;
            $this->twoStepReportsUrl = $authentication->defaultRouteeConfigUrls->twoStepReportsUrl;
            $this->twoStepReportsAppUrl = $authentication->defaultRouteeConfigUrls->twoStepReportsAppUrl;
            

            if(isset($authDecodeResponse->status) &&  $authDecodeResponse->status == 401){
                $this->accessToken = '';
            }

            $this->httpConfigObj = new core\RouteeHttpConfig( null, $this->httpMethod, $this->config );
            $this->accessToken = isset($authDecodeResponse->access_token) ? $authDecodeResponse->access_token : '';      
            $this->header = array(
                'Content-Type'  => "application/json",
                'Authorization' => "Bearer {$this->accessToken}"       
            ); 
        }catch( Exception $e ){
            return new exceptions\RouteeConnectionException( $e );
        }
	}
   
    /**
	 * Static function which reurns new TwoStep Object.
	 * @param array $config
	 * @return string JSON 
	 */
	
	static function getInstance( $config )
	{
	    return new self( $config );
	}
	
    /**
     * This function is used to execute the CURL.
     * 
     * @param array $exeData
     * @param string $httpMethod (GET,POST,PUT,DELETE)
     * @return string JSON 
     */

	private function executeCall( $exeData = array(), $httpMethod = '')
	{
		$config  = isset( $exeData['config'] ) ? $exeData['config'] : $this->config;
		$url     = isset( $exeData['url'] ) ? $exeData['url'] : '';
		$headers = isset( $exeData['header'] ) ? $exeData['header'] : $this->header;
		$data    = isset( $exeData['data'] ) ? $exeData['data'] : '';
		$httpMethod = ( isset($httpMethod) && $httpMethod != '') ? $httpMethod : $this->httpMethod;

		$this->httpConfigObj->setUrl( $url );
		$this->httpConfigObj->setHeaders( $headers );
		$this->httpConfigObj->setMethod( $httpMethod );

		$this->httpConnObj = new core\RouteeHttpConnection( $this->httpConfigObj, $config );

		return $this->httpConnObj->execute( $data );
	}

    /**
	 * Used for retrieving the status of a 2step verification.
	 * @param array $data
	 * KEY | OPTIONAL | DESCRIPTION
	 * ------------ | ------------- | -------------
	 * method | No | The method which will be used to send the 2step verification.
	 * type | No | The type of the message.
	 * recipient | No | The recipient that will receive the 2step verification. For sms method format with a '+' and country code e.g., +306948530920 (E.164 format).
	 * template | Yes | The template of the message. It must contain a @@pin that will be replaced by the generated code.
	 * arguments | Yes	| If the template is for example '@@name your code is @@pin' and the argument has a property name: 'Nick' the message will be 'Nick your code is 4232'. Note that if the template contains a @@ placeholder and a value is not present in the arguments property it will stay as is.
	 * templateCountry	| Yes | Country in ISO-3166-1 alpha 2 format (GR, US etc.). The country to use in order to select a translated template (if defined in Routee web interface)
	 * originator | Yes | The senderId that will be set when sending the SMS
	 * lifetimeInSeconds | Yes | How many seconds this verification will remain active. After that time passes the verification status will be Expired.
	 * maxRetries | Yes | Defines the number of times the user can re-confirm the verification before the verification changes its state to Failed.
	 * digits | Yes | The number of digits of the generated random numeric code.
	 * @return string JSON
	 * <code>
	 * {
     *    "method":"string",
     *    "type":"string",
     *    "recipient":"string",
     *    "arguments":{
     *       "string":"string"
     *     },
     *    "template":"string",
     *    "templateCountry":"string",
     *    "originator":"string",
     *    "lifetimeInSeconds":"number",
     *    "maxRetries":"number",
     *    "digits":"number"
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to reference this specific verification.
     * status | The status of the verification.
     * updatedAt | The last time this verification was updated.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * $data = array(
     *    'method' => 'sms',
     *    'type'   => 'code',
     *    'recipient'   => '+919600951898'
	 *    );
	 * echo  $twostep->start2StepVerification($data);
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function start2StepVerification( $data = array())
	{
	    if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		if(isset($data) && count($data) == 0){
			return json_encode(array('status'=>404,'message'=>'Parameter required'));
		}

	    $executeData = array(
	        'data'  => json_encode( $data ),	    
	        'url'    => $this->twoStepVerifyUrl,
	    );

		return $this->executeCall( $executeData, 'POST');
	}
   
    /**
	 * Retrieve the status of a 2step verification by providing its trackingId.
	 * @param  string $trackID
	 * NAME	| DESCRIPTION
	 * ------------ | -------------
	 * trackingId | the tracking id of the verification.
	 * @return string JSON
	 * <code>
	 * {
     *    "trackingId":"string",
     *    "status":"string",
     *    "updatedAt":"date"
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to reference this specific verification.
     * status | The status of the verification.
     * updatedAt | The last time this verification was updated.
	 * @example 
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * echo $twostep->retrieve2StepStatus('52040307-2179-49da-8291-83bbfd4ac4d3') ;
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function retrieve2StepStatus( $trackID = '' )
	{		
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		if(isset($trackID) && empty($trackID)){
			return json_encode(array('status'=>404,'message'=>'Track id is required'));
		}

		$executeData = array(
	        'url'    => $this->twoStepVerifyUrl.'/'.$trackID,
	    );

		return $this->executeCall( $executeData);
	}

	/**
	 * Cancel a 2step verification by providing its trackingId.
	 * Note that the verification must have a Pending status. 
	 * @param  string $trackID
	 * NAME	| DESCRIPTION
	 * ------------ | -------------
	 * trackingId | the tracking id of the verification.
	 * @return string JSON
	 * <code>
	 * {
     *    "trackingId":"string",
     *    "status":"string",
     *    "updatedAt":"date"
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to reference this specific verification.
     * status | The status of the verification.
     * updatedAt | The last time this verification was updated.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * echo $twostep->cancel2StepStatus('1b5df1e8-3701-41e5-9c41-00904532b656');
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function cancel2StepStatus( $trackID )
	{		
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}
		
		$executeData = array(
	        'url'    => $this->twoStepVerifyUrl.'/'.$trackID,
	    );

		return $this->executeCall( $executeData,'DELETE');
	}
	
	/**
	 * Confirm a 2Step verification by providing the answer. 
	 * @param  array $data
	 * @param  string $trackID
	 * NAME	| DESCRIPTION
	 * ------------ | -------------
	 * trackingId | the tracking id of the verification.
	 * 
	 * KEY | OPTIONAL | DESCRIPTION
	 * ------------ | ------------- | -------------
	 * answer | No | The answer of the verification.
	 * @return String JSON
	 * <code>
	 * {
     *    "trackingId":"string",
     *    "status":"string",
     *    "updatedAt":"date"
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to reference this specific verification.
     * status | The status of the verification.
     * updatedAt | The last time this verification was updated.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * $data = array('answer'=>'6036');
     * echo $twostep->confirm2StepStatus($data,'52040307-2179-49da-8291-83bbfd4ac4d3');
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function confirm2StepStatus( $data , $trackID )
	{		
		if(empty($this->accessToken))
		{
			return $this->returnResponse;
		}
		
		$executeData = array(
			'data'   => http_build_query($data),
	        'url'    => $this->twoStepVerifyUrl.'/'.$trackID,
	        'header' => array(
	                     'Content-Type'  => "application/x-www-form-urlencoded",
	                     'Authorization' => "Bearer {$this->accessToken}"       
	        ),
	    );

		return $this->executeCall( $executeData,'POST');
	}

	/**
	 * Retrieve 2Step verification reports from all of your applications. 
	 * @return String JSON
	 * <code>
	 * {
     *    "total":"number",
     *    "totals":{
     *       "string":"number"
     *    },
     *    "perCountry":{
     *       "string":{
     *          "string":"number"
     *       }
     *    }
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * total | The total number of verifications sent.
     * totals | Count per verification status.
     * perCountry | Count per country and verification status.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * echo $twostep->retrieve2StepAccountReport();
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function retrieve2StepAccountReport()
	{	
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$executeData = array(			
	        'url'    => $this->twoStepReportsUrl,	        
	    );

		return $this->executeCall( $executeData,'GET');
	}

	/**
	 * Retrieve 2Step verification reports for any of your applications by providing the application id.
	 * @param  array $data
	 * NAME	| DESCRIPTION
	 * ------------ | -------------
	 * appId | Your application id
	 * @return String JSON
	 * <code>
	 * {
     *    "applicationId":"string",
     *    "total":"number",
     *    "totals":{
     *       "string":"number"
     *    },
     *    "perCountry":{
     *       "string":{
     *          "string":"number"
     *       }
     *    }
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * applicationId | The application id.
     * total | The total number of verifications sent by this application.
     * totals | Count per verification status.
     * perCountry | Count per country and verification status.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $twostep = api\TwoStep::getInstance($config);
     * $app_id  = 'Your routee application-id';
     * echo $twostep->retrieve2StepAppReport($app_id);
	 * </code>
	 * @throws RouteeConnectionException
	 */

	public function retrieve2StepAppReport( $appId )
	{		
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$executeData = array(			
	        'url'    => $this->twoStepReportsAppUrl.'/'.$appId,	        
	    );

		return $this->executeCall( $executeData,'GET');
	}
}
