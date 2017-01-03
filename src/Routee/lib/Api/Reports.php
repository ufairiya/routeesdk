<?php
/**
 *
 * Routee provides an API for retrieving reports for SMS campaigns
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
 * Class Reports
 *
 * Routee provides an API for retrieving reports for SMS campaigns
 */

class Reports
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

            $this->volPriceUrl = $authentication->defaultRouteeConfigUrls->volPriceUrl;
            $this->volPricePerMessageUrl = $authentication->defaultRouteeConfigUrls->volPricePerMessageUrl;
            $this->volPricePerMsgCountryNtwrkUrl = $authentication->defaultRouteeConfigUrls->volPricePerMsgCountryNtwrkUrl;
            $this->volPricePerCampaignUrl = $authentication->defaultRouteeConfigUrls->volPricePerCampaignUrl;
            $this->msgRangeAnalyticsUrl = $authentication->defaultRouteeConfigUrls->msgRangeAnalyticsUrl;
            $this->latencyPerCountryUrl = $authentication->defaultRouteeConfigUrls->latencyPerCountryUrl;
            $this->latencyPerCountryPerNtwrkUrl = $authentication->defaultRouteeConfigUrls->latencyPerCountryPerNtwrkUrl;
            $this->latencyPerCampaignUrl = $authentication->defaultRouteeConfigUrls->latencyPerCampaignUrl;
            
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
	 * @return Accounts Object.
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
	 * Used to view volume/price summary analytics for a range of messages.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate | No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * @return string JSON
	 * <code>
	 * [
     *    {
     *       "country":"string",
     *       "operator ":"string",
     *       "mcc":"string",
     *       "mnc":"string",
     *       "startDateTime":"date",
     *       "timeGrouping":"string",
     *       "smsCampaignId":"string",
     *       "count":"number",
     *       "deliveredCount":"number",
     *       "failedCount":"number",
     *       "queuedCount":"number",
     *       "sentCount":"number",
     *       "undeliveredCount":"number",
     *       "price ":"number"
     *    }
     * ]
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * country | The country’s code in ISO 3166­1 alpha­2 format.
     * operator | The operator’s name.
     * mcc | The mobile country code.
     * mnc | The mobile network code.
     * startDateTime | The date and time of the first SMS of this report.
     * timeGrouping | The time interval that the reports are grouped by.
     * smsCampaignId | The ID of the campaign that the SMS belongs to.
     * count | The total messages count.
     * deliveredCount | The amount of the delivered messages.
     * failedCount | The amount of the failed messages.
     * queuedCount | The amount of the queued messages.
     * sentCount | The amount of the sent messages.
     * undeliveredCount | The amount of the undelivered messages.
     * price | The total price of this report.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *   'startDate' => '2015-01-01T00:00:00.000Z',
	 *   'endDate' => '2017-01-01T00:00:00.000Z'
 	 *   );
 	 * echo $reports->viewMsgRangeAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException 
	 */
	
	public function viewMsgRangeReport( $param )
	{
	    if(empty($this->accessToken)){
		    return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );
		
		$executeData = array(
	        'url'    =>  $this->volPriceUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);
	}

    /**
	 * Used to view volume/price summary analytics for a country.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | -------------  | ------------- | -------------
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate | No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * mcc | No | the mcc code | 202
	 * @return string JSON
	 * <code>
	 * [
     *    {
     *       "country":"string",
     *       "operator ":"string",
     *       "mcc":"string",
     *       "mnc":"string",
     *       "startDateTime":"date",
     *       "timeGrouping":"string",
     *       "smsCampaignId":"string",
     *       "count":"number",
     *       "deliveredCount":"number",
     *       "failedCount":"number",
     *       "queuedCount":"number",
     *       "sentCount":"number",
     *       "undeliveredCount":"number",
     *       "price ":"number"
     *    }
     * ]
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * country | The country’s code in ISO 3166­1 alpha­2 format.
     * operator | The operator’s name.
     * mcc | The mobile country code.
     * mnc | The mobile network code.
     * startDateTime | The date and time of the first SMS of this report.
     * timeGrouping | The time interval that the reports are grouped by.
     * smsCampaignId | The ID of the campaign that the SMS belongs to.
     * count | The total messages count.
     * deliveredCount | The amount of the delivered messages.
     * failedCount | The amount of the failed messages.
     * queuedCount | The amount of the queued messages.
     * sentCount | The amount of the sent messages.
     * undeliveredCount | The amount of the undelivered messages.
     * price | The total price of this report.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'startDate' => '2015-01-01T00:00:00.000Z',
	 *    'endDate' => '2017-01-01T00:00:00.000Z',
	 *    'mcc' => '404'
	 *    );
	 * echo $reports->viewCountryAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException 
	 */
	
	public function viewCountryAnalytics($param )
	{
		if(empty($this->accessToken)){
		    return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );
		
		$executeData = array(
	        'url'    => $this->volPricePerMessageUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);
	}
   
    /**
	 * Used to view volume/price summary analytics for a country and network.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate | No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * mcc | No | the mcc code | 202
	 * mnc | No | the mnc code | 08
	 * @return string JSON
	 * <code>
	 * [
     *    {
     *       "country":"string",
     *       "operator ":"string",
     *       "mcc":"string",
     *       "mnc":"string",
     *       "startDateTime":"date",
     *       "timeGrouping":"string",
     *       "smsCampaignId":"string",
     *       "count":"number",
     *       "deliveredCount":"number",
     *       "failedCount":"number",
     *       "queuedCount":"number",
     *       "sentCount":"number",
     *       "undeliveredCount":"number",
     *       "price ":"number"
     *    }
     * ]
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * country | The country’s code in ISO 3166­1 alpha­2 format.
     * operator | The operator’s name.
     * mcc | The mobile country code.
     * mnc | The mobile network code.
     * startDateTime | The date and time of the first SMS of this report.
     * timeGrouping | The time interval that the reports are grouped by.
     * smsCampaignId | The ID of the campaign that the SMS belongs to.
     * count | The total messages count.
     * deliveredCount | The amount of the delivered messages.
     * failedCount | The amount of the failed messages.
     * queuedCount | The amount of the queued messages.
     * sentCount | The amount of the sent messages.
     * undeliveredCount | The amount of the undelivered messages.
     * price | The total price of this report.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'startDate' => '2015-01-01T00:00:00.000Z',
	 *    'endDate' => '2017-01-01T00:00:00.000Z',
	 *    'mcc' => '404',
	 *    'mnc' => '43'
	 *    );
	 * echo $reports->viewVolPriceCntryNtwrk( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function viewVolPriceCntryNtwrk( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    => $this->volPricePerMsgCountryNtwrkUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);
	}

    /**
	 * Used to view volume/price summary analytics for a campaign.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION	| EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * offset | No | The time offset that the result will be calculated in ISO 8601. | 2016-01-31T00:00:00.000Z
	 * campaignId | No | The id of the campaign that the messages belong to. | 7c22af9e-d998-4d69-a4ea-574037294d45
	 * @return string JSON
	 * <code>
	 * [
     *    {
     *       "country":"string",
     *       "operator ":"string",
     *       "mcc":"string",
     *       "mnc":"string",
     *       "startDateTime":"date",
     *       "timeGrouping":"string",
     *       "smsCampaignId":"string",
     *       "count":"number",
     *       "deliveredCount":"number",
     *       "failedCount":"number",
     *       "queuedCount":"number",
     *       "sentCount":"number",
     *       "undeliveredCount":"number",
     *       "price ":"number"
     *    }
     * ]
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * country | The country’s code in ISO 3166­1 alpha­2 format.
     * operator | The operator’s name.
     * mcc | The mobile country code.
     * mnc | The mobile network code.
     * startDateTime | The date and time of the first SMS of this report.
     * timeGrouping | The time interval that the reports are grouped by.
     * smsCampaignId | The ID of the campaign that the SMS belongs to.
     * count | The total messages count.
     * deliveredCount | The amount of the delivered messages.
     * failedCount | The amount of the failed messages.
     * queuedCount | The amount of the queued messages.
     * sentCount | The amount of the sent messages.
     * undeliveredCount | The amount of the undelivered messages.
     * price | The total price of this report.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'offset' => '+02:00', 
	 *    'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5', 
	 *    );
     * echo $reports->viewCampaignAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function viewCampaignAnalytics( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    =>  $this->volPricePerCampaignUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);		
	}

    /**
	 * Used to view time summary analytics for a range of messages.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate | No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * @return string JSON
	 * <code>
	 * {  
     *    "smsLatencyCount":[  
     *       "number"
     *    ]
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * smsLatencyCount | An array containing the amount of messages by their latency.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'startDate' => '2015-01-01T00:00:00.000Z',
	 *    'endDate' => '2017-01-01T00:00:00.000Z'
	 *    );
	 * echo $reports->viewCampaignAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function viewMsgRangeAnalytics( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    => $this->msgRangeAnalyticsUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);		
	}
   
    /**
	 * Used to view time summary analytics for a country.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION	| EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * countryCode	| No | The country’s code in ISO 3166­1 alpha­2 format | GR
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate	| No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * @return string JSON
	 * <code>
	 * {  
     *    "smsLatencyCount":[  
     *       "number"
     *    ]
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * smsLatencyCount | An array containing the amount of messages by their latency.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'startDate' => '2015-01-01T00:00:00.000Z',
	 *    'endDate' => '2017-01-01T00:00:00.000Z',
	 *    'countryCode' => 'IN'
 	 *    );
 	 * echo $reports->viewTimeCountryAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */
	
	public function viewTimeCountryAnalytics( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    => $this->latencyPerCountryUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);		
	}
   
    /**
	 * Used to view time summary analytics for a country and a network.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * startDate | No | starting date to get reports | 2016-01-31T00:00:00.000Z
	 * endDate	| No | ending date to get reports | 2016-01-31T00:00:00.000Z
	 * mcc | No | the mcc code | 202
	 * mnc | No | the mnc code | 08
	 * @return string JSON
	 * <code>
	 * {  
     *    "smsLatencyCount":[  
     *       "number"
     *    ]
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * smsLatencyCount | An array containing the amount of messages by their latency.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'startDate' => '2015-01-01T00:00:00.000Z',
	 *    'endDate' => '2017-01-01T00:00:00.000Z',
	 *    'mcc' => '404',
	 *    'mnc' => '43'
	 *    );
	 * echo $reports->viewTimeCntryNtwrkAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */

	public function viewTimeCntryNtwrkAnalytics( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    => $this->latencyPerCountryPerNtwrkUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);		
	}
   
    /**
	 * Used to view time summary analytics for a campaign.
	 * @param array $param
	 * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
	 * ------------ | ------------- | ------------- | -------------
	 * campaignId | No | The Id of the campaign | 7c22af9e-d998-4d69-a4ea-574037294d45
	 * @return string JSON
	 * <code>
	 * {  
     *    "smsLatencyCount":[  
     *       "number"
     *    ]
     * }
	 * </code>
	 * KEY | DESCRIPTION
     * ------------ | -------------
     * smsLatencyCount | An array containing the amount of messages by their latency.
	 * @example
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
	 * use Routee\lib\Api as api;
     * $reports = api\Reports::getInstance($config);
     * $data = array(
	 *    'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5'
	 * );
	 * echo $reports->viewCampaignTimeAnalytics( $data );
	 * </code>
	 * @throws RouteeConnectionException
	 */
	public function viewCampaignTimeAnalytics( $param )
	{
		if(empty($this->accessToken)){
			return $this->returnResponse;
		}

		$queryParam = http_build_query( $param );

		$executeData = array(
	        'url'    => $this->latencyPerCampaignUrl.'?'.$queryParam
	    );

		return $this->executeCall( $executeData);		
	}
}
