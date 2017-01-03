<?php
/**
 *
 * Routee provides an API to send SMS messages to and from any country across the world.
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
 * Class Messaging
 *
 * Messages are identified by a unique tracking Id. 
 * With this Id you can always check the status of the message through the provided endpoint.
 */

class Messaging 
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
     * Default Constructor
     * This function used to get the access token from the authenticate the user credentials
     * @param array $config
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

            $this->messagingSendSingleSMSUrl = $authentication->defaultRouteeConfigUrls->messagingSendSingleSMSUrl;
            $this->analyzeSingleMessageUrl = $authentication->defaultRouteeConfigUrls->analyzeSingleMessageUrl;
            $this->messagingSendSMSCampaignUrl = $authentication->defaultRouteeConfigUrls->messagingSendSMSCampaignUrl;
            $this->analyzeSMSCampaignMessageUrl = $authentication->defaultRouteeConfigUrls->analyzeSMSCampaignMessageUrl;
            $this->trackingSingleSMSUrl = $authentication->defaultRouteeConfigUrls->trackingSingleSMSUrl;
            $this->trackingCampaignSMSUrl = $authentication->defaultRouteeConfigUrls->trackingCampaignSMSUrl;
            $this->trackingSMSUrl = $authentication->defaultRouteeConfigUrls->trackingSMSUrl;
            $this->countriesQuietHrsUrl = $authentication->defaultRouteeConfigUrls->countriesQuietHrsUrl;
            $this->SMSUrl = $authentication->defaultRouteeConfigUrls->SMSUrl;
            $this->campaignsUrl = $authentication->defaultRouteeConfigUrls->campaignsUrl;              

            if(isset($authDecodeResponse->status) &&  $authDecodeResponse->status == 401){
                  $this->accessToken = '';
            }
            
            $this->httpConfigObj = new core\RouteeHttpConfig( null, $this->httpMethod, $this->config );
            $this->accessToken = isset($authDecodeResponse->access_token) ? $authDecodeResponse->access_token : '';      
            $this->header = array(
               'Content-Type'  => "application/json",
               'Authorization' => "Bearer {$this->accessToken}"       
            );

          }
          catch( Exception $e ){
              return new exceptions\RouteeConnectionException( $e );
          }
         
    }
    
    /**
     * Static function which reurns new TwoStep Object.
     * @param array $config
     * @return srting JSON
     */

    static function getInstance( $config = array())
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
     * Sending a single SMS is one of the most common tasks performed on the Routee Platform. 
     * Sending a message is as simple as POST-ing to the SMS resource.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * from | No | The sender of the message. This can be a telephone number or an alphanumeric string. In case of an alphanumeric string, the maximum length is 11 characters. In case of a numeric only string the length is 16 characters.
     * body | No | The message you want to send.
     * to | No | The destination phone number. Format with a '+' and country code e.g., +306948530920 (E.164 format).
     * flash | Yes | Indicates if the SMS is a flash SMS. A flash SMS is a type of SMS that appears directly on the main screen without user interaction and is not automatically stored in the inbox. It can be useful in emergencies, such as a fire alarm or cases of confidentiality, as in delivering one-time passwords.
     * label | Yes | A generic label which can be used for tagging the SMS. The maximum length is 20 characters.
     * callback | Yes | Defines the callback information for an individual message.
     * callback.url | Yes | A URL that Routee will POST to, each time your message status changes to one of the following: Queued, Failed, Sent, Delivered, or Undelivered. Routee will POST the trackingId along with the other request parameters as well as statuses and ErrorDescriptions.
     * callback.strategy | Yes | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion). 
     * @return string JSON
     * <code>
     * {  
     *    "createdAt":"date",
     *    "flash":"boolean",
     *    "body":"string",
     *    "to":"string",
     *    "from":"string",
     *    "bodyAnalysis":{
     *       "characters":"number",
     *       "parts":"number",
     *       "transcode":{
     *          "message":"string",
     *          "parts":"number"
     *       },
     *       "unicode":"boolean",
     *       "unsupportedGSMCharacters":[
     *          "string"
     *       ]
     *    },
     *    "callback":{
     *       "strategy":"string",
     *       "url":"string"
     *    },
     *    "status":"string",
     *    "label":"string",
     *    "trackingId":"string"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to identify the message.
     * createdAt | The date that this resource was created.
     * from | The sender of the message.
     * to | The phone number the message is about to be sent to.
     * body | The message you sent.
     * status | The status of the SMS.
     * label | The label that was given to the SMS.
     * bodyAnalysis | The analysis for the body of the SMS.
     * bodyAnalysis.characters | The total number of characters of the message body.
     * bodyAnalysis.parts | The number of actual SMS that will be sent.
     * bodyAnalysis.transcode | Contains information for the transcoded body of the SMS message. This will be available only if the message can be transcoded.
     * bodyAnalysis.transcode.message | The transcoded message of the original unicode message.
     * bodyAnalysis.unicode | Indicates if the body contains unicode characters.
     * bodyAnalysis.unsupportedGSMCharacters | Which characters caused the message to be considered as unicode.
     * flash | Indicates if the message is flash SMS.
     * callback | Defines the notification callback information for an individual SMS message.
     * callback.url | The URL that Routee will POST to, each time your message status changes to one of the following: Queued, Failed, Sent, Delivered, or Undelivered.
     * callback.strategy | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $data_sms = array(
     *   'body'=>'A new game has been posted to the MindPuzzle. Check it out',
     *   'to'=> '+306973359355',
     *   'from'=> 'amdTelecom',
     *   'flash'=> false,
     *   'label'=>'Routee',
     *   'callback' => array(
     *   'strategy' => 'OnChange',
     *       'url' => 'http://www.yourserver.com/message',
     *       )
     *    );
     * echo $send_sms = $sms->sendSingleSMS($data_sms);
     * </code>
     * @throws RouteeConnectionException
     */

    public function sendSingleSMS($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
            
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->messagingSendSingleSMSUrl
        );                    
            
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }
      
    /**        
     * Analyzing a single SMS is useful when the user needs information about the message before actually sending it.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * from | No | The sender of the message. This can be a telephone number or an alphanumeric string. In case of an alphanumeric string, the maximum length is 11 characters. In case of a numeric only string the length is 16 characters.
     * body | No | The message you want to send.
     * to | No | The destination phone number. Format with a '+' and country code e.g., +306948530920 (E.164 format).
     * @return string JSON
     * <code>
     * {
     *    "bodyAnalysis":{
     *       "characters":"number",
     *       "parts":"number",
     *       "transcode":{
     *          "message":"string",
     *          "parts":"number"
     *       },
     *       "unicode":"boolean",
     *       "unsupportedGSMCharacters":[
     *          "string"
     *       ]
     *    },
     *    "cost":"number"
     *  }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * bodyAnalysis | The analysis for the body of the SMS.
     * bodyAnalysis.characters | The total number of characters of the message body.
     * bodyAnalysis.parts | The number of actual SMS that will be sent.
     * bodyAnalysis.transcode | Contains information for the transcoded body of the sms message. This will be available only if the message can be transcoded.
     * bodyAnalysis.transcode.message | The transcoded message of the original unicode message.
     * bodyAnalysis.unicode | Indicates if the body contains unicode characters.
     * bodyAnalysis.unsupportedGSMCharacters | Which characters caused the message to be considered as unicode.
     * cost | The cost of the SMS.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $data_sms = array(
     *   'body'=>'A new game has been posted to the MindPuzzle. Check it out',
     *   'to'=> '+306973359355',
     *   'from'=> 'amdTelecom',     
     *   );
     * echo $send_sms = $sms->getAnalyzeSingleMessage($data_sms);
     * </code>
     * @throws RouteeConnectionException
     */

    public function getAnalyzeSingleMessage($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
            
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->analyzeSingleMessageUrl, 
        );                    
             
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }
      
    /**        
     * Routee allows to send an SMS campaign to multiple recipients/contacts/groups.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * contacts | Yes | The contact ids that the message will be sent.
     * groups | Yes | The groups of contacts in the account selected as recipients.
     * to | Yes | The phone numbers the message is about to be sent to. Format with a '+' and country code e.g., +306948530920 (E.164 format).
     * from | No | The sender of the message. This can be a telephone number or an alphanumeric string. In case of an alphanumeric string, the maximum length is 11 characters. In case of a numeric only string the length is 16 characters
     * body | No | The message you want to send.
     * scheduledDate | Yes | The datetime the campaign will run.
     * campaignName | Yes | The name of the campaign. If you want to be able to track the whole campaign from Routee console use a name, if no name is provided you won’t be able to see the campaign from the console but you can track all the individual messages.
     * flash | Yes | Indicates if the SMS is a flash SMS. A flash SMS is a type of SMS that appears directly on the main screen without user interaction and is not automatically stored in the inbox. It can be useful in emergencies, such as a fire alarm or cases of confidentiality, as in delivering one-time passwords.
     * respectQuietHours | Yes | Indicates if the SMS should respect the quiet hours. Quiet Hours are set by default to 23.00 - 08.00 and 14.00-17.00 destination local time. Please note that not all countries are supported with this feature due to multiple time zones within the country.
     * campaignCallback | Yes | Defines the notification callback information for the progress of the SMS campaign.
     * campaignCallback.url | No | The URL that Routee will POST to, each time your campaign status changes to one of the following: Scheduled, Queued, Sent, Running, Finished, or Failed.
     * campaignCallback.strategy | No | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * callback | Yes | Defines the notification callback information for an individual message progress of the SMS campaign.
     * callback.url | No | The URL that Routee will POST to, each time your message status changes to one of the following: Queued, Failed, Sent, Delivered, or Undelivered.
     * callback.strategy | No | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion)
     * reminder | Yes | Defines the recipients that will receive a test SMS before and/or after the actual SMS is sent.
     * reminder.minutesAfter | Yes | The minutes after the scheduled date (that the SMS will be sent) that the test SMS will be sent.
     * reminder.minutesBefore | Yes | The minutes before the scheduled date (that the SMS will be sent) that the test SMS will be sent.
     * reminder.to | No | The recipients that will get the test SMS before and/or after the campaign will start. Must be a list with valid mobile numbers starting with “ + ” and the country code.
     * fallbackValues | Yes | Defines the default values when the SMS has labels, in case a contact does not contain any of these labels. 
     * @return string JSON
     * <code>
     *  {
     *      "campaignCallback":{
     *         "strategy":"string",
     *         "url":"string"
     *      },
     *      "contacts":[
     *         "string"
     *      ],
     *      "createdAt":"date",
     *      "fallbackValues":{
     *         "string":"string"
     *      },
     *      "flash":"boolean",
     *      "groups":[
     *         "string"
     *      ],
     *      "body":"string",
     *      "campaignName":"string",
     *      "to":[
     *         "string"
     *      ],
     *      "respectQuietHours":"boolean",
     *      "scheduledDate":"date",
     *      "from":"string",
     *      "smsAnalysis":{
     *         "bodyAnalysis":{
     *            "characters":"number",
     *            "parts":"number",
     *            "transcode":{
     *               "message":"string",
     *               "parts":"number"
     *            },
     *            "unicode":"boolean",
     *            "unsupportedGSMCharacters":[
     *               "string"
     *            ]
     *         },
     *         "contacts":{
     *            "string":{
     *               "recipient":"string",
     *               "recipientCountry":"string",
     *               "blacklisted":"boolean"
     *            }
     *         },
     *         "numberOfRecipients":"number",
     *         "recipientCountries":{
     *            "string":"string"
     *         },
     *         "recipientsPerCountry":{
     *            "string":"number"
     *         },
     *         "recipientsPerGroup":{
     *            "string":"number"
     *         },
     *         "totalInGroups":"number"
     *      },
     *      "callback":{
     *         "strategy":"string",
     *         "url":"string"
     *      },
     *      "state":"string",
     *      "statuses":{
     *         "string":"number"
     *      },
     *      "reminder":{
     *         "minutesAfter":"number",
     *         "minutesBefore":"number",
     *         "to":[
     *            "string"
     *         ]
     *      },
     *      "trackingId":"string",
     *      "type":"string"
     *  }
     * </code> 
     * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to identify the campaign.
     * createdAt | The date that this resource was created.
     * type | The service type of the campaign.
     * scheduledDate | The time the campaign is scheduled to run.
     * campaignName | The name of the campaign.
     * from | The sender of the message.
     * to | The phone numbers the message is about to be sent to.
     * body | The message you sent.
     * state | The state of the campaign.
     * statuses | Defines the number of SMS per message status. Either, Queued, Sent, Failed, Delivered, Undelivered
     * smsAnalysis | The data analysis the this SMS.
     * smsAnalysis.contacts | The details for each contact. The key refers to the id of the contact given in the request.
     * smsAnalysis.contacts.recipient | The mobile phone that corresponds to the given contact id.
     * smsAnalysis.contacts.recipientCountry | The country that corresponds to the given contact id.
     * smsAnalysis.contacts.blacklisted | Indicates if the contact is in the SMS blacklist. If true the contact will be excluded from the request.
     * smsAnalysis.numberOfRecipients | The total number of recipients.
     * smsAnalysis.recipientCountries | The country that each mobile belongs to. The key refers to the mobile of the recipients request property
     * smsAnalysis.recipientsPerCountry | The total recipients per country. The key refers to the country.
     * smsAnalysis.recipientsPerGroup | For each group the number of recipients that it contains without the blacklisted contacts (in SMS blacklist). The key refers to the group given in the group request property.
     * smsAnalysis.totalInGroups | The total number of recipients in all given groups excluding the ones already specified (in contacts and recipients request property) as well as the ones that are blacklisted.
     * smsAnalysis.bodyAnalysis | The analysis for the body of the SMS.
     * smsAnalysis.bodyAnalysis.characters | The total number of characters of message body.
     * smsAnalysis.bodyAnalysis.parts | The number of actual SMS that will be sent.
     * smsAnalysis.bodyAnalysis.transcode | Contains information for the transcoded body of the SMS message. This will be available only if the message can be transcoded.
     * smsAnalysis.bodyAnalysis.transcode.message | The transcoded message of the original unicode message.
     * smsAnalysis.bodyAnalysis.transcode.parts | The number of actual SMS that will be sent.
     * smsAnalysis.bodyAnalysis.unicode | Indicates if the body contains unicode characters.
     * smsAnalysis.bodyAnalysis.unsupportedGSMCharacters | Which characters caused the message to be considered as unicode.
     * flash | Indicates if the message is a flash SMS.
     * respectQuietHours | Indicates if the SMS should respect the quiet hours.
     * campaignCallback | Defines the notification callback information for the progress of the SMS campaign.
     * campaignCallback.url | The URL that Routee will POST to, each time your campaign status changes to one of the following: Scheduled, Queued, Sent, Running, Finished, or Failed.
     * campaignCallback.strategy | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * callback | Defines the notification callback information for an individual message progress of the SMS campaign.
     * callback.url | The URL that Routee will POST to, each time your message status changes to one of the following: Queued, Failed, Sent, Delivered, or Undelivered.
     * callback.strategy | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * reminder | Defines the recipients that will receive a test SMS before and/or after the actual SMS is sent.
     * reminder.minutesAfter | The minutes after the scheduled date (that the SMS will be sent) that the test SMS will be sent.
     * reminder.minutesBefore | The minutes before the scheduled date (that the SMS will be sent) that the test SMS will be sent.
     * reminder.to | The recipients that will get the test SMS before and/or after the campaign will start. Must be a list with valid mobile numbers starting with “ + ” and the country code.
     * contacts | The contacts in the account selected as recipients.
     * groups | The groups of contacts in the account selected as recipients.
     * fallbackValues | Defines the default values when the SMS has labels, in case a contact does not contain any of these labels. The key refers to the label name. 
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $data_camp = array(
     *    'body'=>'Hello [~firstName] a new version of MindPuzzle is available. Check it out',        
     *    'from'=> 'mindpuzzle',       
     *    'reminder' => array(
     *        'minutesAfter'=> '5',
     *        'minutesBefore'=> '5',
     *        'to' => array('+306973359355'),
     *     ),
     *     'callback' => array(
     *         "strategy" => "OnChange",
     *         "url"=>"http://www.yourserver.com/campaign"
     *     ),
     *     'flash' => false,
     *     'smsCallback' => array(
     *         "strategy" => "OnChange",
     *         "url"=>"http://www.yourserver.com/SMScampaign"
     *     ),
     *     'campaignName' => 'mindpuzzle Customer',       
     *     'to' => array('+306973359355'),
     *     'fallbackValues' => array('firstName'=>'Kesav'),
     * );
     *  echo $send_camp = $sms->sendCampaign($data_camp);
     * </code>
     * @throws RouteeConnectionException
     */

    public function sendCampaign($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->messagingSendSMSCampaignUrl, 
        );             
        
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }

        return $response;

    }            
  
    /**        
     * Analyzing a campaign is useful when the user needs information about the campaign before actually sending it.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * contacts | Yes | The contact ids that the message will be sent.
     * groups | Yes | The groups of contacts in the account selected as recipients.
     * to | Yes | The phone numbers the message is about to be sent to. Format with a '+' and country code e.g., +306948530920 (E.164 format).
     * from | No | The sender of the message. This can be a telephone number or an alphanumeric string. In case of an alphanumeric string, the maximum length is 11 characters. In case of a numeric only string the length is 16 characters.
     * body | No | The message you want to send.
     * @return string JSON
     * <code>
     * {
     *    "bodyAnalysis":{
     *       "characters":"number",
     *       "parts":"number",
     *       "transcode":{
     *          "message":"string",
     *          "parts":"number"
     *       },
     *       "unicode":"boolean",
     *       "unsupportedGSMCharacters":[
     *          "string"
     *       ]
     *    },
     *    "contacts":{
     *       "string":{
     *          "recipient":"string",
     *          "recipientCountry":"string",
     *          "blacklisted":"boolean"
     *       }
     *    },
     *    "numberOfRecipients":"number",
     *    "recipientCountries":{
     *       "string":"string"
     *    },
     *    "recipientsPerCountry":{
     *       "string":"number"
     *    },
     *    "recipientsPerGroup":{
     *       "string":"number"
     *    },
     *    "totalInGroups":"number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * contacts | The details for each contact. The key refers to the id of the contact given in the request.
     * contacts.recipient | The mobile phone that corresponds to the given contact id.
     * smsAnalysis.contacts.recipientCountry | The country that corresponds to the given contact id.
     * contacts.blacklisted | Indicates if the contact is in the SMS blacklist. If true the contact will be excluded from the request.
     * numberOfRecipients | The total number of recipients.
     * recipientCountries | The country that each mobile belongs to. The key refers to the mobile of the recipients request property.
     * recipientsPerCountry | The total recipients per country. The key refers to the country.
     * recipientsPerGroup | For each group the number of recipients that it contains without the blacklisted contacts (in SMS blacklist). The key refers to the group given in the group request property.
     * totalInGroups | The total number of recipients in all given groups excluding the ones already specified (in contacts and recipients request property) as well as the ones that are blacklisted.
     * bodyAnalysis | The analysis for the body of the SMS.
     * bodyAnalysis.characters | The total number of characters of message body.
     * bodyAnalysis.parts | The number of actual SMS that will be sent.
     * bodyAnalysis.transcode | Contains information for the transcoded body of the sms message. This will be available only if the message can be transcoded.
     * bodyAnalysis.transcode.message | The transcoded message of the original unicode message.
     * bodyAnalysis.transcode.parts | The number of actual SMS that will be sent.
     * bodyAnalysis.unicode | Indicates if the body contains unicode characters.
     * bodyAnalysis.unsupportedGSMCharacters | Which characters caused the message to be considered as unicode.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $analyseCampData = array(
     *    'from'=> 'mindpuzzle', 
     *    'to'    =>  array(
     *                  '+306973359355'
     *      ),
     *    'body'=>'Hello [~firstName] a new version of MindPuzzle is available. Check it out'
     *    
     *     );
     * echo $analyseCampResponse = $sms->analyzeCampaignSMS( $analyseCampData );
     * </code>
     * @throws RouteeConnectionException
     */

    public function analyzeCampaignSMS( $data )
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(
            'data'   => json_encode($data), 
            'url'    => $this->analyzeSMSCampaignMessageUrl, 
        ); 

        try { 
            $response = $this->executeCall($executeData,'POST'); 
        }
        catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        } 
      
        return $response; 

    }

    /**        
     * Use this function to track status of a Single SMS.
     * The function gets only one argument, i.e., the ID of the SMS which was previously sent.
     * Kindly note that TRACKING ID is different from MESSAGE ID.
     * @param string $msgId
     * NAME | DESCRIPTION
     * ------------ | -------------
     * messageId | The id of the single SMS
     * @return string JSON
     * <code>
     * [
     *    {
     *       "applicationName":"string",
     *       "country":"string",
     *       "id":"string",
     *       "groups":[
     *          "string"
     *       ],
     *       "body":"string",
     *       "operator":"string",
     *       "originatingService":"string",
     *       "to":"string",
     *       "label":"string",
     *       "status":{
     *          "date":"date",
     *          "status":"string"
     *       },
     *       "latency":"number",
     *       "parts":"number",
     *       "price":"number"
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * applicationName | The name of the application that was used to send this message.
     * campaign | The name of the campaign that this message was sent from.
     * country | The country of the recipient in ISO 3166-1 alpha 2 format.
     * id | The id of the SMS tracking.
     * groups | The groups that the recipient belongs to.
     * body | The message of the SMS.
     * operator | The operator of the recipient.
     * originatingService | The service that sent this message.
     * to | The recipient.
     * latency | The overall delivery latency of the message.
     * parts | The number of actual SMS parts.
     * price | The cost of this SMS part.
     * label | The label of the SMS tracking.
     * status | The status of the SMS tracking.
     * status.date | The date that the status was reported.
     * status.status | The status.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;     
     * $sms = new api\Messaging($config);
     * $messageID = 'f5b6f428-90a5-46f1-9e3f-c2f170cbe539';
     * echo $trackSMSResponse = $sms->trackSingleSMSbyId( $messageID );
     * </code>
     * @throws RouteeConnectionException
     */

    public function trackSingleSMSbyId( $msgId )
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }

        $executeData = array(
            'data'   => '{}', 
            'url'    => $this->trackingSingleSMSUrl.'/'.$msgId
        ); 

        try { 
            $response = $this->executeCall($executeData, 'GET'); 
        }
        catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        } 
        
        return $response; 
    }

    /**        
     * You can get all the tracking information for the messages of a campaign by providing the campaign tracking id      
     * @param string $campTrackId
     * @param array $param
     * NAME | DESCRIPTION
     * ------------ | -------------
     * campaignTrackingId | The tracking id of the campaign which includes the messages.
     * 
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
     * size | Yes | The number of items to retrieve, default value is 10 | 1
     * sort | Yes | The field name that will be used to sort the results | operator
     * @return string JSON
     * <code>
     * {
     *    "totalPages":"number",
     *    "last":"boolean",
     *    "totalElements":"number",
     *    "first":"boolean",
     *    "numberOfElements":"number",
     *    "size":"number",
     *    "number":"number",
     *    "content":[
     *       {
     *          "applicationName":"string",
     *          "campaign":"string",
     *          "country":"string",
     *          "id":"string",
     *          "groups":[
     *             "string"
     *          ],
     *          "body":"string",
     *          "operator":"string",
     *          "originatingService":"string",
     *          "to":"string",
     *          "status":{
     *             "date":"date",
     *             "status":"string"
     *          },
     *          "latency":"number",
     *          "parts":"number",
     *          "price":"number"
     *       }
     *    ]
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * totalPages | The number of total pages.
     * last | Whether the current page is the last one.
     * totalElements | The total amount of elements for the current search.
     * first | Whether the current page is the first one.
     * numberOfElements | The number of elements currently on this page.
     * number | The requested page number.
     * size | The requested page size.
     * content | Contains the search results.
     * content.applicationName | The name of the application that was used to send this message.
     * content.campaign | The name of the campaign that this message was sent from.
     * content.country | The country of the recipient in ISO 3166-1 alpha 2 format.
     * content.id | The id of the SMS tracking.
     * content.groups | The groups that the recipient belongs to.
     * content.body | The message of the SMS.
     * content.operator | The operator of the recipient.
     * content.originatingService | The service that sent this message.
     * content.to | The recipient.
     * content.latency | The overall delivery latency of the message.
     * content.parts | The number of actual SMS parts.
     * content.price | The cost of this SMS part.
     * content.status | The status of the SMS tracking.
     * content.status.date | The date that the status was reported.
     * content.status.status | The status.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $campaignTrackingId = '125e05df-0dc9-41de-b3f7-4e0f71fe4f04';
     * $param = array('page'=>'0' );
     * echo $campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
     * </code>
     * @throws RouteeConnectionException
     */

    public function trackCampaignMultiSMS( $campTrackId,$param = array() )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        } 

        $executeData = array(
            'data'   => '{}', 
            'url'    => $this->trackingCampaignSMSUrl.'/'.$campTrackId.'?'.http_build_query( $param )
        ); 

        try { 
            $response = $this->executeCall($executeData, 'GET'); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        }

        return $response; 
    }

    /**        
     * Use this function to track multiple sms with filters for a specific time range. 
     * Limit the tracking result of multiple sms by passing filters.  
     * @param array $dataFilters contains two array element namely filter_param  and query_param.
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * dateStart | Yes | ISO-8601 date-time format | 2015-11-11T15:00Z
     * dateEnd | Yes | ISO-8601 date-time format | 2015-11-11T15:00Z
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
     * size | Yes | The number of items to retrieve, default value is 10 | 1
     * sort	| Yes | The field name that will be used to sort the results | operator
     * trackingId | Yes | If provided then only the SMS messages for the specific tracking id will be retrieved | 2c1379bb-f296-43a4-bfb0-6c8b20a97425
     * campaign	| Yes | If true it will return only SMS messages that belong to an SMS campaign | true
     * 
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * fieldName | No | The name of the field to filter.
     * searchTerm | No | The exact value that the specified field must match.
     * @return string JSON
     * <code>
     * {
     *    "totalPages":"number",
     *    "last":"boolean",
     *    "totalElements":"number",
     *    "first":"boolean",
     *    "numberOfElements":"number",
     *    "size":"number",
     *    "number":"number",
     *    "content":[
     *       {
     *          "applicationName":"string",
     *          "campaign":"string",
     *          "country":"string",
     *          "id":"string",
     *          "groups":[
     *             "string"
     *          ],
     *          "body":"string",
     *          "operator":"string",
     *          "originatingService":"string",
     *          "to":"string",
     *          "status":{
     *             "date":"date",
     *          "label":"string",
     *          "status":"string"
     *          },
     *          "latency":"number",
     *          "parts":"number",
     *          "price":"number"
     *       }
     *    ]
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * totalPages | The number of total pages.
     * last | Whether the current page is the last one.
     * totalElements | The total amount of elements for the current search.
     * first | Whether the current page is the first one.
     * numberOfElements | The number of elements currently on this page.
     * number | The requested page number.
     * size | The requested page size.
     * content | Contains the search results.
     * content.applicationName | The name of the application that was used to send this message.
     * content.campaign | The name of the campaign that this message was sent from.
     * content.country | The country of the recipient in ISO 3166-1 alpha 2 format.
     * content.id | The id of the SMS tracking.
     * content.groups | The groups that the recipient belongs to.
     * content.body | The message of the SMS.
     * content.operator | The operator of the recipient.
     * content.originatingService | The service that sent this message.
     * content.to | The recipient.
     * content.latency | The overall delivery latency of the message.
     * content.parts | The number of actual SMS parts.
     * content.price | The cost of this SMS part.
     * content.label | The label that was given to the SMS tracking.
     * content.status | The status of the SMS tracking.
     * content.status.date | The date that the status was reported.
     * content.status.status | The status.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config );
     * $data = array(
     *          'filter_param' => array(
     *                 array(
     *                        'fieldName'  => 'smsId',
     *                        'searchTerm' => '335c5ec5-bc82-415d-af94-ad884da23d56'
     *                )
     *          ),
     *          'query_param' => array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z')
     * );
     * echo $SMSfilterResponse = $sms->filterMultipleSMS( $data );
     * </code>
     * @throws RouteeConnectionException
     */ 

    public function filterMultipleSMS( $dataFilters = array() )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        }
        
        if(is_array($dataFilters) && count($dataFilters) > 0 && !isset($dataFilters['filter_param']) && !isset($dataFilters['query_param'])){
          return json_encode(array('status'=>401,'message'=>'please provide filter_param or query param'));
        }

        $data = (is_array($dataFilters) && isset($dataFilters['filter_param']) ) ? $dataFilters['filter_param'] :array();  
        $param = (is_array($dataFilters) && isset($dataFilters['query_param']) ) ? $dataFilters['query_param'] :array();
        
        $executeData = array(
            'data'   => json_encode( $data ), 
            'url'    => $this->trackingSMSUrl,
        );

        $queryParam =(is_array($param) && count($param) > 0) ?  $executeData['url'].'?'.http_build_query( $param ) : '';
         
        try { 
            $response = $this->executeCall( $executeData ,'POST'); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        } 

        return $response;
    }

    /**        
     * You can retrieve all the supported countries in your preferred language.  
     * @param string $langCode
     * NAME | DESCRIPTION
     * ------------ | -------------
     * language | The language code is ISO 639-1 format (el, en) that will be used to translate the country names.
     * @return string JSON
     * <code>
     * [
     *    {
     *       "code":"string",
     *       "name":"string",
     *       "localeName":"string",
     *       "supported": "boolean"
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * code | The country code in ISO 3166-1 alpha-2 format (US, GR).
     * name | The country name translated in the requested language.
     * localeName | The country name translated in its native language (for Greece it will be Ελλάδα).
     * supported | If the country is supported or not.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $countryID = 'en';
     * echo $retriveCountriesResponse = $sms->retriveCountriesQuietHour( $countryID );
     * </code>
     * @throws RouteeConnectionException
     */

    public function retriveCountriesQuietHour( $langCode = '' )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        }

        $executeData = array(
            'data'   => '{}', 
            'url'    => $this->countriesQuietHrsUrl.'/'.$langCode
        );

        try { 
            $response = $this->executeCall( $executeData, 'GET' ); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        }

        return $response;
    }

    /**        
     * Update an already scheduled campaign. You can change the recipients 
     * or the sender id or any other parameter you set previously. 
     * Note that the campaign must have status "Scheduled" in order to update it.
     * @param array $data
     * @param string $campaignID
     * NAME | DESCRIPTION
     * ------------ | -------------
     * campaignTrackingId | the campaign’s tracking id.
     * @return string JSON
     * <code>
     * {
     *    "callback":{
     *       "strategy":"string",
     *       "url":"string"
     *    },
     *    "contacts":[
     *       "string"
     *    ],
     *    "createdAt":"date",
     *    "fallbackValues":{
     *       "string":"string"
     *    },
     *    "flash":"boolean",
     *    "groups":[
     *       "string"
     *    ],
     *    "body":"string",
     *    "campaignName":"string",
     *    "to":[
     *       "string"
     *    ],
     *    "respectQuietHours":"boolean",
     *    "scheduledDate":"date",
     *    "from":"string",
     *    "smsAnalysis":{
     *       "bodyAnalysis":{
     *          "characters":"number",
     *          "parts":"number",
     *          "transcode":{
     *             "message":"string",
     *             "parts":"number"
     *          },
     * 
     *          "unicode":"boolean",
     *          "unsupportedGSMCharacters":[
     *             "string"
     *          ]
     *       },
     *       "contacts":{
     *          "string":{
     *             "recipient":"string",
     *             "recipientCountry":"string",
     *             "blacklisted":"boolean"
     *          }
     *       },
     *       "numberOfRecipients":"number",
     *       "recipientCountries":{
     *          "string":"string"
     *       },
     *       "recipientsPerCountry":{
     *          "string":"number"
     *       },
     *       "recipientsPerGroup":{
     * 
     *          "string":"number"
     *       },
     *       "totalInGroups":"number"
     *    },
     *    "campaignCallback":{
     *       "strategy":"string",
     *       "url":"string"
     *    },
     *    "state":"string",
     *    "statuses":{
     *       "string":"number"
     *    },
     *    "reminder":{
     *       "minutesAfter":"number",
     *       "minutesBefore":"number",
     *       "to":[
     *          "string"
     *       ]
     *    },
     *    "trackingId":"string",
     *    "type":"string"
     * }
     * </code>
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $updateData = array(
     *     'from' => 'amdTelecome',
     *     'callback' => array(
     *             'url' => 'http://www.yourserver.com/SMScampaign'
     *         ),
     *     'contacts' => array( ),     
     *     'flash' => false,
     *     'groups' => array( ),
     *     'body' => 'Hi! This is a updated Campaign message 03.',
     *     'campaignName' => 'Campaign-Update',
     *     'to' => array(
     *             '0' => '+306984512344'
     *         ),
     *     'respectQuietHours' => true,
     *     'scheduledDate' => '2016-09-20T09:26:53Z'
     * 
     * );
     * echo $updateScheduledCampaignResponse = $sms->updateScheduledCampaign( $updateData, 'a23e8ffc-f606-49aa-98c4-d46a83ed41f1' );
     * </code>
     * @throws RouteeConnectionException
     */

    public function updateScheduledCampaign( $data, $campaignID = '' )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        }

        $executeData = array(
            'data'   => json_encode( $data ), 
            'url'    => $this->SMSUrl.'/'.$campaignID
        );

        try { 
            $response = $this->executeCall( $executeData, 'PUT' ); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        }

        return $response;
    }

    /**        
     * When you no longer want to send an already scheduled campaign, deleting it is very simple.  
     * @param string $campTrackingID
     * NAME | DESCRIPTION
     * ------------ | -------------
     * trackingId | the campaign’s tracking id.
     * @return string JSON
     * <code>
     * </code>
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $delCampTrID = 'a23e8ffc-f606-49aa-98c4-d46a83ed41f1';
     * echo $deleteScheduledCampaignResponse = $sms->deleteScheduledCampaign( $delCampTrID );
     * </code>
     * @throws RouteeConnectionException
     */
    
    public function deleteScheduledCampaign( $campTrackingID = '' )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        }

        $executeData = array(
            'data'   => '{}', 
            'url'    => $this->SMSUrl.'/'.$campTrackingID
        ); 

        try { 
            $response = $this->executeCall( $executeData, 'DELETE' ); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        }

        return $response;
    }
  
    /**        
     * You can retrieve the details of a specific campaign by its tracking id.
     * @param string $campTrackingID
     * NAME | DESCRIPTION
     * ------------ | -------------
     * trackingId | the campaign’s tracking id. 
     * @return string JSON
     * <code>
     * {
     *    "callback":{
     *       "strategy":"string",
     *       "url":"string"
     *    },
     *    "contacts":[
     *       "string"
     *    ],
     *    "createdAt":"date",
     *    "fallbackValues":{
     *       "string":"string"
     *    },
     *    "flash":"boolean",
     *    "groups":[
     *       "string"
     *    ],
     *    "body":"string",
     *    "campaignName":"string",
     *    "to":[
     *       "string"
     *    ],
     *    "respectQuietHours":"boolean",
     *    "scheduledDate":"date",
     *    "from":"string",
     *    "smsAnalysis":{
     *       "bodyAnalysis":{
     *          "characters":"number",
     *          "parts":"number",
     *          "transcode":{
     *             "message":"string",
     *             "parts":"number"
     *          },
     *          "unicode":"boolean",
     *          "unsupportedGSMCharacters":[
     *             "string"
     *          ]
     *       },
     *       "contacts":{
     *          "string":{
     *             "recipient":"string",
     *             "recipientCountry":"string",
     *             "blacklisted":"boolean"
     *          }
     *       },
     *       "numberOfRecipients":"number",
     *       "recipientCountries":{
     *          "string":"string"
     *       },
     *       "recipientsPerCountry":{
     *          "string":"number"
     *       },
     *       "recipientsPerGroup":{
     *          "string":"number"
     *       },
     *       "totalInGroups":"number"
     *    },
     *    "campaignCallback":{
     *       "strategy":"string",
     *       "url":"string"
     *    },
     *    "state":"string",
     *    "statuses":{
     *       "string":"number"
     *    },
     *    "reminder":{
     *       "minutesAfter":"number",
     *       "minutesBefore":"number",
     *       "recipients":[
     *          "string"
     * 
     *      ]
     *    },
     *    "trackingId":"string",
     *    "type":"string"
     * }
     * </code> 
     * KEY | DESCRIPTION
     * ------------ | -------------
     * trackingId | The tracking id used to identify the campaign.
     * createdAt | The date that this resource was created.
     * type | The service type of the campaign.
     * scheduledDate | The time the campaign is scheduled to run.
     * campaignName | The name of the campaign.
     * from | The sender of the message.
     * to | The phone numbers the message is about to be sent to.
     * body | The message you send.
     * state | The status of the campaign.
     * statuses | Defines the number of SMS per message status. Either, Queued, Sent, Failed, Delivered, Undelivered.
     * smsAnalysis | The data analysis the this SMS.
     * smsAnalysis.contacts | The details for each contact. The key refers to the id of the contact given in the request.
     * smsAnalysis.contacts.recipient | The mobile phone that corresponds to the given contact id.
     * smsAnalysis.contacts.recipientCountry | The country that corresponds to the given contact id.
     * smsAnalysis.contacts.blacklisted | Indicates if the contact is in the SMS blacklist. If true the contact will be excluded from the request.
     * smsAnalysis.numberOfRecipients | The total number of recipients.
     * smsAnalysis.recipientCountries | The country that each mobile belongs to. The key refers to the mobile of the recipients request property.
     * smsAnalysis.recipientsPerCountry | The total recipients per country. The key refers to the country.
     * smsAnalysis.recipientsPerGroup | For each group the number of recipients that it contains without the blacklisted contacts (in SMS blacklist). The key refers to the group given in the group request property.
     * smsAnalysis.totalInGroups | The total number of recipients in all given groups excluding the ones already specified (in contacts and recipients request property) as well as the ones that are blacklisted.
     * smsAnalysis.bodyAnalysis | The analysis for the body of the SMS.
     * smsAnalysis.bodyAnalysis.characters | The total number of characters of message body.
     * smsAnalysis.bodyAnalysis.parts | The number of actual SMS that will be sent.
     * smsAnalysis.bodyAnalysis.transcode | Contains information for the transcoded body of the SMS message. This will be available only if the message can be transcoded.
     * smsAnalysis.bodyAnalysis.transcode.message | The transcoded message of the original unicode message.
     * smsAnalysis.bodyAnalysis.transcode.parts | The number of actual SMS that will be sent.
     * smsAnalysis.bodyAnalysis.unicode | Indicates if the body contains unicode characters.
     * smsAnalysis.bodyAnalysis.unsupportedGSMCharacters | Which characters caused the message to be considered as unicode.
     * flash | Indicates if the message is a flash SMS.
     * respectQuietHours | Indicates if the SMS should respect the quiet hours.
     * callback | Defines the notification callback information for the progress of the SMS campaign.
     * callback.url | The URL that Routee will POST to, each time your campaign status changes to one of the following: Scheduled, Queued, Sent, Running, Finished, or Failed.
     * callback.strategy | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * campaignCallback | Defines the notification callback information for an individual message progress of the SMS campaign.
     * campaignCallback.url | The URL that Routee will POST to, each time your message status changes to one of the following: Queued, Failed, Sent, Delivered, or Undelivered.
     * campaignCallback.strategy | When the URL will be called. Two possible values: on every status change (OnChange) or when a final status arrives (OnCompletion).
     * reminder | Defines the recipients that will receive a test SMS before and/or after the actual SMS will be sent.
     * reminder.minutesAfter | The minutes after the scheduled date (that the SMS will be send) that the test SMS will be sent.
     * reminder.minutesBefore | The minutes before the scheduled date (that the SMS will be send) that the test SMS will be sent.
     * reminder.to | The recipients that will get the test SMS before and/or after the campaign will start. Must be a list with valid mobile numbers starting with “ + ” and the country code.
     * contacts | The contacts in the account selected as recipients.
     * groups | The groups of contacts in the account selected as recipients.
     * fallbackValues | Defines the default values when the SMS has labels, in case a contact does not contain any of these labels. The key refers to the label name.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $sms = new api\Messaging($config);
     * $retrieveCampID = '0b978991-2b39-4b8b-874a-e449e792e020';
     * echo $retrieveCampaignResponse = $sms->retrieveDetailsCampaign( $retrieveCampID );
     * </code>
     * @throws RouteeConnectionException
     */
  
    public function retrieveDetailsCampaign( $campTrackingID = '' )
    {
        if( empty( $this->accessToken ) ) {
            return $this->returnResponse; 
        }

        $executeData = array(
            'data'   => '{}', 
            'url'    => $this->campaignsUrl.'/'.$campTrackingID
        ); 
        
        try { 
            $response = $this->executeCall( $executeData, 'GET' ); 
        }catch(Exception $e){
            $ex = new exceptions\RouteeConnectionException($e); 
            throw $ex; 
        }

        return $response;
    }
}
