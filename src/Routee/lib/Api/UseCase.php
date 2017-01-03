<?php
/**
 * Usecase for Routee SDK
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
 * Class UseCase
 *
 *
 * @package Routee\lib\Api
 *
 */

class UseCase
{
    /**
	 * Use Case 1:I want to get authorized with application credentials, create a contact with custom 
	 * labels (first create the labels (one text label and one numeric label) and then use them for the 
	 * creation of the contact), create a new group and add this contact to the group. 
	 * 
	 * STEPS | DESCRIPTION
     * ------------ | -------------
	 * 1. | Get authorized
	 * 2. | Create a custom numeric lable named "cats"
	 * 3. | Create a custom text label named "address"
	 * 4. | Create a new contact that has 5 cats and lives at "Some address"
	 * 5. | Create a new group called "PeopleWithCats"
	 * 6. | Add the contact to the group
	 * 7. | Retrieve the details of the contact to see if the contact has the group attached to it
	 * 
	 * @example Code
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * use Routee\lib\Api as api;
	 * $config = array(
	 *     'application-id' => '57b5b7bde4b007f5ba82952b',
	 *     'application-secret' => '6k6sitD5hU',    
	 * );
	 * 
	 * // Step 1:  Get authorized
	 * $authResponse = new api\Authorization();
	 * $authResult = $authResponse->getAuthorization($config);
	 * $authResultDecode = json_decode($authResult);
	 *  
	 * // Step 2:  Create a custom numeric label named "cats"
	 * $data = array(	
	 * array(
	 *        'name' => 'cats',
	 *        'type' => 'Number'
	 * 	)
	 * );
	 * $contactResponse = new api\Contacts($config);
	 * $contactlabelResult = $contactResponse->createLabel($data);
	 * $ContactlabelDecode = json_decode($contactlabelResult);
	 * 
	 * // Step 3:  Create a custom text label named "address"
	 * $data_group_address = array(
	 * 	array(
	 *        'name' => 'address',
	 *        'type' => 'Text'
	 * 	)
	 * );
	 * $contactlabelAddrResult = $contactResponse->createLabel($data_group_address);
	 * $contactlabelAddrDecode = json_decode($contactlabelAddrResult);
	 * 
	 * // Step 4:  Create a new contact that has 5 cats and lives at "Some Address"
	 * $data_contact = array(
	 * 	           'firstName' => 'kesava',
	 * 	           'lastName' => 'moorthi m',
	 * 	           'mobile' => '+919025060261',
	 * 	           'vip' => 'false',
	 * 	           'labels'=> array(
	 * 		           	 array(
	 * 		           	 	'name' => 'cats',
	 * 		           	 	'type' => 'Number',	           	    
	 * 		             	'value' => 5,
	 * 		           	 ),
	 * 		           	 array(
	 * 		           	 	'name' => 'address',
	 * 		           	    'value' => 'Some Address',
	 * 		           	 ),
	 * 	           	)
	 * );
	 * $contactResult = $contactResponse->createContacts( $data_contact );
	 * $contactDecode = json_decode($contactResult);
	 * 
	 * // Step 5:  Create a new group called "PeopleWithCats"
	 * $group_name = 'PeopleWithCats';
	 * $data_group = array(
	 *     'name' => $group_name,         
	 * );
	 * $contactGroupResult = $contactResponse->createGroup($data_group);
	 * $contactGroupDecode = json_decode($contactGroupResult);
	 * 
	 * // Step 6: Add the contact to the group
	 * $data_group_name = array($contactDecode->id);
	 * $contactGroupAddResult = $contactResponse->addContactsToGroupByName($data_group_name,$group_name);
	 * $contactGroupContactDecode = json_decode($contactGroupAddResult);
	 * 
	 * // Step 7:  Retrieve the details of the contact to see if the contact has the group attached to it
	 * $contactSingleResult = $contactResponse->retrieveSingleContacts($contactDecode->id );
	 * $contactSingleResultDecode = json_decode($contactSingleResult);
	 * 
	 * echo '<pre>';print_r($contactSingleResultDecode); echo '</pre>';
	 * </code>
	 * @example Response
	 * 
	 * <code>
	 * Step 1: Get authorized - Result
	 * -------------------------------
	 * stdClass Object
	 * (
	 *     [access_token] => 7f93272e-a473-4257-9ff2-7e9b3a55bb49
	 *     [token_type] => bearer
	 *     [expires_in] => 3423
	 *     [scope] => contact sms report account
	 *     [permissions] => Array
	 *         (
	 *             [0] => MT_ROLE_ACCOUNT_FINANCE
	 *             [1] => MT_ROLE_SMS
	 *             [2] => MT_ROLE_REPORT
	 *             [3] => MT_ROLE_2STEP
	 *             [4] => MT_ROLE_CONTACT
	 *         )
	 * )
	 * 
	 * Step 2: Create a custom numeric label named "cats" - Result
	 * -----------------------------------------------------------
	 * Array
	 * (
	 *     [0] => stdClass Object
	 *         (
	 *             [name] => cats
	 *             [type] => Number
	 *         )
	 * )
	 * 
	 * Step 3: Step 3:  Create a custom text label named "address" - Result
	 * --------------------------------------------------------------------
	 * Array
	 * (
	 *     [0] => stdClass Object
	 *         (
	 *             [name] => address
	 *             [type] => Text
	 *         )
	 * )
	 * 
	 * Step 4: Create a new contact that has 5 cats and lives at "Some Address" - Result
	 * ---------------------------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [id] => 57df63d60cf2232979762d5e
	 *     [firstName] => kesava
	 *     [lastName] => moorthi m
	 *     [mobile] => +919025060261
	 *     [country] => IN
	 *     [vip] => 
	 *     [groups] => Array
	 *         (
	 *             [0] => All
	 *             [1] => PeopleWithCats
	 *         )
	 *     [blacklistedServices] => Array
	 *         (
	 *         )
	 *     [labels] => Array
	 *         (
	 *             [0] => stdClass Object
	 *                 (
	 *                     [name] => cats
	 *                     [value] => 5
	 *                     [type] => Number
	 *                 )
	 * 
	 *             [1] => stdClass Object
	 *                 (
	 *                     [name] => address
	 *                     [value] => Some Address
	 *                     [type] => Text
	 *                 )
	 *         )
	 * )
	 * 
	 * Step 5: Create a new group called "PeopleWithCats" - Result
	 * -----------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [name] => PeopleWithCats
	 *     [size] => 0
	 * )
	 * 
	 * Step 6: Add the contact to the group - Result
	 * ---------------------------------------------
	 * stdClass Object
	 * (
	 *     [name] => PeopleWithCats
	 *     [size] => 1
	 * )
	 * 
	 * Step 7: Retrieve the details of the contact to see if the contact has the group attached to it - Result
	 * --------------------------------------------------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [id] => 57df63d60cf2232979762d5e
	 *     [firstName] => kesava
	 *     [lastName] => moorthi m
	 *     [mobile] => +919025060261
	 *     [country] => IN
	 *     [vip] => 
	 *     [groups] => Array
	 *         (
	 *             [0] => All
	 *             [1] => PeopleWithCats
	 *         )
	 *     [blacklistedServices] => Array
	 *         (
	 *         )
	 *     [labels] => Array
	 *         (
	 *             [0] => stdClass Object
	 *                 (
	 *                     [name] => cats
	 *                     [value] => 5
	 *                 )
	 *             [1] => stdClass Object
	 *                 (
	 *                     [name] => address
	 *                     [value] => Some Address
	 *                 )
	 *         )
	 * )
	 * 
	 * </code> 
	 */	 
	public function usecase1()
	 {
		 
	 }
	/**
	 * Use Case 2:​ I want to get authorized with application credentials, send a single sms and track it 
	 * filtered by senderId
	 * 
	 * STEPS | DESCRIPTION
     * ------------ | -------------
	 * 1. | Get authorized
	 * 2. | Send a single SMS to a mobile number and "To":"amdTelecom"
	 * 3. | Track your messages with filters and use fieldName: "To" and "searchTerm":"amdTelecom"\
	 * 
	 * @example code
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * use Routee\lib\Api as api;
	 * $config = array(
	 *      'application-id' => '57b5b7bde4b007f5ba82952b',
	 *      'application-secret' => '6k6sitD5hU',    
	 * );
	 * 
	 * // Step 1:  Get authorized
	 * $authResponse = new api\Authorization();
	 * $authResult = $authResponse->getAuthorization($config);
	 * $authResultDecode = json_decode($authResult);
	 * 
	 * // Step 2:  Send a single SMS to a mobile number and “To”:”amdTelecom”
	 * $sms = new api\Messaging($config);
	 *     $data_sms = array(
     *         'body'=>'Test Message- AMDTelecom Routee Api',
     *         'to'=> 'amdTelecom',
     *         'from'=> 'kesav',
     *         'flash'=> false,
     *         'label'=>'AMDTelecom Routee',
     *         'callback' => array(
     *             'strategy' => 'OnChange',
     *             'url' => 'http://www.yourserver.com/message/callback.php',
     *             )
     *         );
     * $sendSmsResult = $sms->sendSingleSMS($data_sms);
     * $sendSmsResultDecode = json_decode($sendSmsResult);
     * $data_sms_success = array(
     *     'body'=>'Test Message- AMDTelecom Routee Api',
     *     'to'=> '919787136232',
     *     'from'=> 'kesav',
     *     'flash'=> false,
     *     'label'=>'AMDTelecom Routee',
     *     'callback' => array(
     *         'strategy' => 'OnChange',
     *         'url' => 'http://www.yourserver.com/message/callback.php',
     *         )
     * );
     * $sendSmsSuccessResult = $sms->sendSingleSMS($data_sms_success);
     * $sendSmsSuccessResultDecode = json_decode($sendSmsSuccessResult);
     * 
     * //Step 3:  Track your messages with filters and use fieldName: "To" and "searchTerm":"amdTelecom"
     * 
     * $data_filter = array(
     *     'filter_param' => array(
     *         array(
     *             'fieldName'  => 'to',
     *             'searchTerm' => 'amdTelecom'
     *             )
     *         ),
     *               
     * );
     * $SMSfilterResponse = $sms->filterMultipleSMS( $data_filter );
     * $SMSfilterResponseDecode = json_decode($SMSfilterResponse);
     * 
     * $data_filters = array(
     *     'filter_param' => array(
     *         array(
     *             'fieldName'  => 'to',
     *             'searchTerm' => '+917871962432'
     *              )
     *         ),              
     * );
     * $SMSfilterResponseB = $sms->filterMultipleSMS( $data_filters );
     * $SMSfilterResponseBDecode = json_decode($SMSfilterResponseB);
     * </code>
     * @example Response
     * 
     * <code>
     * Step 1: Get authorized - Result
     * -------------------------------------
     * stdClass Object
     * (
     *     [access_token] => 7f93272e-a473-4257-9ff2-7e9b3a55bb49
     *     [token_type] => bearer
     *     [expires_in] => 630
     *     [scope] => contact sms report account
     *     [permissions] => Array
     *         (
     *             [0] => MT_ROLE_ACCOUNT_FINANCE
     *             [1] => MT_ROLE_SMS
     *             [2] => MT_ROLE_REPORT
     *             [3] => MT_ROLE_2STEP
     *             [4] => MT_ROLE_CONTACT
     *         )
     * )
     * 
     * Step 2 a): Send a single SMS to a mobile number and "To":"amdTelecom" - Result
     * ----------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [code] => 400000000
     *     [developerMessage] => Validation Error!
     *     [entity] => SmsTracking
     *     [properties] => stdClass Object
     *         (
     *             [to] => Invalid mobile number
     *         )
     * )
     * 
     * Step 2 b): Send a single SMS to a mobile number and "To":"919787136232" - Result
     * ----------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [trackingId] => 1fd5355b-4963-495e-8208-eb93dda8d124
     *     [status] => Queued
     *     [createdAt] => 2016-09-19T07:54:50.95Z
     *     [from] => kesav
     *     [to] => +919787136232
     *     [body] => Test Message- AMDTelecom Routee Api
     *     [bodyAnalysis] => stdClass Object
     *         (
     *             [parts] => 1
     *             [unicode] => 
     *             [characters] => 34
     *         )
     *     [flash] => 
     *     [callback] => stdClass Object
     *         (
     *             [url] => http://www.yourserver.com/message/callback.php
     *             [strategy] => OnChange
     *         )
     *     [label] => AMDTelecom Routee
     * )
     * 
     * Step 3 a): Track your messages with filters and use fieldName: "To" and "searchTerm":"amdTelecom" - Result
     * -------------------------------------------------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [content] => Array
     *         (
     *         )
     *     [totalPages] => 0
     *     [totalElements] => 0
     *     [last] => 1
     *     [numberOfElements] => 0
     *     [first] => 1
     *     [size] => 20
     *     [number] => 0
     * )
     * 
     * Step 3 b): Track your messages with filters and use fieldName: "To" and "searchTerm":"917871962432" - Result
     * -------------------------------------------------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [content] => Array
     *         (
     *             [0] => stdClass Object
     *                 (
     *                     [smsId] => 9742aef4-dbef-4bd8-abde-bccf3f45ad11
     *                     [messageId] => 3da376a8-943a-4dc0-a509-2169af8a4897
     *                     [to] => +917871962432
     *                     [groups] => Array
     *                         (
     *                         )
     *                     [country] => IN
     *                     [operator] => Airtel (Bharti Airtel)
     *                     [status] => stdClass Object
     *                         (
     *                             [status] => Delivered
     *                             [date] => 2016-09-19T06:46:33Z
     *                         )
     *                     [body] => Your code is 2622
     *                     [applicationName] => php
     *                     [originatingService] => TwoStep
     *                     [latency] => 3
     *                     [parts] => 1
     *                     [price] => 0.08
     *                     [from] => verify
     *                     [direction] => Outbound
     *                 )
     *      ..........
     *         )
     *     [totalPages] => 1
     *     [totalElements] => 11
     *     [last] => 1
     *     [numberOfElements] => 11
     *     [first] => 1
     *     [size] => 20
     *     [number] => 0
     * )
     * </code>
     */
	 public function usecase2()
	 {
		 
	 }
	/**
	 * Use Case 3: ​I want to send an SMS campaign with campaign callback to one of my contacts 
	 * and a recipient, retrieve its details and track its messages.
	 * 
	 * STEPS | DESCRIPTION
     * ------------ | -------------
     * 1. | Get authorized
	 * 2. | Create a contact
	 * 3. | Send a campaign to this contact and an additional recipient. Make sure you add campaign callback details.
	 * 4. | Make sure you receive campaign callback for the campaign you just sent.
	 * 5. | Retrieve the details of the campaign (use the https://connect.routee.net/campaigns/{trackingId ​ }  ​ endpoint)
	 * 6. | Track the messages of the campaign to see if they were both delivered.
	 * 
	 * @example code
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * use Routee\lib\Api as api;
	 * $config = array(
     *     'application-id' => '57b5b7bde4b007f5ba82952b',
     *     'application-secret' => '6k6sitD5hU',    
     * );
     * 
     * // Step 1:  Get authorized
     * $authResponse = new api\Authorization();
     * $authResult = $authResponse->getAuthorization($config);
     * $authResultDecode = json_decode($authResult);
     * 
     * // Step 2:  Create a contact
     * $data_contact = array(
     *     'firstName' => 'Gokul',
     *     'lastName' => 'kumar statllioni',
     *     'mobile' => '+919787136232',
     *     'vip' => 'false',                      
     * );
     * $contactResponse = new api\Contacts($config);
     * $contactResult = $contactResponse->createContacts( $data_contact );
     * $contactDecode = json_decode($contactResult);
     * 
     * // Step 3:  Send a campaign to this contact and an additional recipient. Make sure you add campaign callback details
     * 
     * $data_camp = array(
     *     'body'=>'Hi [~firstName] Test Message- stallioni Routee Api',        
     *     'from'=> '919600951898',       
     *     'contacts'=>array($contactDecode->id),        
     *     'callback' => array(
     *         "strategy" => "OnChange",
     *         "url"=>"http://www.yourserver.com/message/callback.php"
     *         ),
     *     'flash' => false,
     *     'smsCallback' => array(
     *         "strategy" => "OnChange",
     *         "url"=>"http://www.yourserver.com/message/callback.php"
     *         ),        
     *     'campaignName' => 'API-'.time(),
     *     'to' => array('+917871962432'),      
     *     'fallbackValues' => array('firstName'=>'Gokul','firstName'=>'Subash'),
     * );
     * $sms = new api\Messaging($config);
     * $sendCampResult = $sms->sendCampaign($data_camp);
     * $sendCampResultDecode = json_decode($sendCampResult);
     * 
     * // Step 4: Make sure you receive campaign callback for the campaign you just sent.
     * 
     *  callback.php
     *  ------------
     *  $webhookContent = '';
     *  $webhook = fopen('php://input' , 'rb');
     *  while (!feof($webhook)) {
     *      $webhookContent .= fread($webhook, 4096);
     *      }
     *  fclose($webhook);
     *  echo $webhookContent;
     *  
     * // Step 5: Retrieve the details of the campaign (use the https://connect.routee.net/campaigns/{trackingId} endpoint)
     * // Step 6: Track the messages of the campaign to see if they were both delivered
     * $campaignTrackingId = $sendCampResultDecode->trackingId;
     * $param = array('page'=>'0' );
     * $campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
     * $campaignTrackingDecode = json_decode($campaignTrackingResponse);
     * echo '<pre>'; 
     * print_r($campaignTrackingDecode);
     * exit;
	 * </code>
	 * 
	 * @example Response
	 * <code>
	 * Step 1: Get authorized - Result
	 * -------------------------------------
	 * stdClass Object
	 * (
     *     [access_token] => 8029f88d-ce61-4b87-b2be-1260d1b605a0
     *     [token_type] => bearer
     *     [expires_in] => 3599
     *     [scope] => contact sms report account
     *     [permissions] => Array
     *         (
     *             [0] => MT_ROLE_ACCOUNT_FINANCE
     *             [1] => MT_ROLE_SMS
     *             [2] => MT_ROLE_REPORT
     *             [3] => MT_ROLE_2STEP
     *             [4] => MT_ROLE_CONTACT
     *         )
     * )
     * 
     * Step 2: Create a contact - Result
     * -------------------------------------
     * stdClass Object
     * (
     *     [id] => 57df6bd40cf2232979762e18
     *     [firstName] => Gokul
     *     [lastName] => kumar statllioni
     *     [mobile] => +919787136232
     *     [country] => IN
     *     [vip] => 
     *     [groups] => Array
     *         (
     *             [0] => All
     *             [1] => NotListed
     *         )
     *     [blacklistedServices] => Array
     *         (
     *         )
     *     [labels] => Array
     *         (
     *         )
     * )
     * Step 3: Send a campaign to this contact and an additional recipient. Make sure you add campaign callback details - Result
     * ------------------------------------------------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [campaignName] => API-1474276611
     *     [trackingId] => 941c7c3f-c45f-4d35-9739-6c67f06c5228
     *     [type] => Sms
     *     [state] => Queued
     *     [createdAt] => 2016-09-19T09:16:53.344Z
     *     [respectQuietHours] => 
     *     [from] => 919600951898
     *     [to] => Array
     *         (
     *             [0] => +917871962432
     *         )
     *     [contacts] => Array
     *         (
     *             [0] => 57df6bd40cf2232979762e18
     *         )
     *     [body] => Hi [~firstName] Test Message- stallioni Routee Api
     *     [smsAnalysis] => stdClass Object
     *         (
     *             [numberOfRecipients] => 2
     *             [recipientsPerCountry] => stdClass Object
     *                 (
     *                     [IN] => 2
     *                 )
     *             [recipientCountries] => stdClass Object
     *                 (
     *                     [+917871962432] => IN
     *                 )
     *             [contacts] => stdClass Object
     *                 (
     *                     [57df6bd40cf2232979762e18] => stdClass Object
     *                         (
     *                             [recipient] => +919787136232
     *                             [recipientCountry] => IN
     *                             [blacklisted] => 
     *                         )
     *                 )
     *             [recipientsPerGroup] => stdClass Object
     *                 (
     *                 )
     *             [totalInGroups] => 0
     *             [bodyAnalysis] => stdClass Object
     *                 (
     *                     [parts] => 1
     *                     [unicode] => 
     *                     [characters] => 50
     *                 )
     *         )
     *     [flash] => 
     *     [fallbackValues] => stdClass Object
     *         (
     *             [firstName] => Subash
     *         )
     *     [statuses] => stdClass Object
     *         (
     *             [Queued] => 0
     *             [Sent] => 0
     *             [Failed] => 0
     *             [Delivered] => 0
     *             [Undelivered] => 0
     *         )
     *     [callback] => stdClass Object
     *         (
     *             [url] => http://www.yourserver.com/message/callback.php
     *             [strategy] => OnChange
     *         )
     *     [cost] => 0
     *     [totalMessages] => 2
     * )
     * 
     * Step 4: campaign callback  - Result
     * ------------------------------------------------------------------------------------------------------------------
     * {"messageId":"9571ef84-6e34-4601-ba3c-7a5748bc8e7f","campaignTrackingId":"941c7c3f-c45f-4d35-9739-6c67f06c5228","to":"+919787136232","from":"919600951898","country":"IN","operator":"Vodafone","groups":[],"campaignName":"API-1474276611","status":{"name":"Queued","updatedDate":"2016-09-19T09:16:54Z"},"message":"Hi Gokul Test Message- stallioni Routee Api","applicationName":"default","latency":0,"parts":1,"price":0.00600000,"direction":"Outbound","originatingService":"Sms"}
     * {"messageId":"6ab6a203-bb5e-4cb6-964c-bd3e3fa1602a","campaignTrackingId":"941c7c3f-c45f-4d35-9739-6c67f06c5228","to":"+917871962432","from":"919600951898","country":"IN","operator":"Airtel (Bharti Airtel)","groups":[],"campaignName":"API-1474276611","status":{"name":"Sent","updatedDate":"2016-09-19T09:16:54Z"},"message":"Hi Subash Test Message- stallioni Routee Api","applicationName":"default","latency":0,"parts":1,"price":0.00600000,"direction":"Outbound","originatingService":"Sms"}
     * 
     * Step 5: Retrieve the details of the campaign - Result
     * ------------------------------------------------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [content] => Array
     *         (
     *             [0] => stdClass Object
     *                 (
     *                     [smsId] => 89d475e1-7560-4713-a70e-ec9be722cf0d
     *                     [messageId] => 6ab6a203-bb5e-4cb6-964c-bd3e3fa1602a
     *                     [to] => +917871962432
     *                     [groups] => Array
     *                         (
     *                         )
     *                     [country] => IN
     *                     [operator] => Airtel (Bharti Airtel)
     *                     [status] => stdClass Object
     *                         (
     *                             [status] => Sent
     *                             [date] => 2016-09-19T09:16:54Z
     *                         )
     *                     [body] => Hi Subash Test Message- stallioni Routee Api
     *                     [campaignName] => API-1474276611
     *                     [applicationName] => default
     *                     [originatingService] => Sms
     *                     [latency] => 0
     *                     [parts] => 1
     *                     [price] => 0.006
     *                     [from] => 919600951898
     *                     [direction] => Outbound
     *                 )
     *             [1] => stdClass Object
     *                 (
     *                     [smsId] => ff17fb17-19d8-43ef-a2f4-73135203553e
     *                     [messageId] => 9571ef84-6e34-4601-ba3c-7a5748bc8e7f
     *                     [to] => +919787136232
     *                     [groups] => Array
     *                         (
     *                         )
     *                     [country] => IN
     *                     [operator] => Vodafone
     *                     [status] => stdClass Object
     *                         (
     *                             [status] => Sent
     *                             [date] => 2016-09-19T09:16:54Z
     *                         )
     *                     [body] => Hi Gokul Test Message- stallioni Routee Api
     *                     [campaignName] => API-1474276611
     *                     [applicationName] => default
     *                     [originatingService] => Sms
     *                     [latency] => 0
     *                     [parts] => 1
     *                     [price] => 0.006
     *                     [from] => 919600951898
     *                     [direction] => Outbound
     *                 )
     *             )
     *     [totalPages] => 1
     *     [totalElements] => 2
     *     [last] => 1
     *     [numberOfElements] => 2
     *     [first] => 1
     *     [size] => 20
     *     [number] => 0
     * )
     * Step 6: Track the messages of the campaign to see if they were both delivered - Result
     * ------------------------------------------------------------------------------------------------------------------
     * stdClass Object
     * (
     *     [content] => Array
     *         (
     *             [0] => stdClass Object
     *                 (
     *                     [smsId] => 89d475e1-7560-4713-a70e-ec9be722cf0d
     *                     [messageId] => 6ab6a203-bb5e-4cb6-964c-bd3e3fa1602a
     *                     [to] => +917871962432
     *                     [groups] => Array
     *                         (
     *                         )
     *                     [country] => IN
     *                     [operator] => Airtel (Bharti Airtel)
     *                     [status] => stdClass Object
     *                         (
     *                             [status] => Sent
     *                             [date] => 2016-09-19T09:16:54Z
     *                         )
     *                     [body] => Hi Subash Test Message- stallioni Routee Api
     *                     [campaignName] => API-1474276611
     *                     [applicationName] => default
     *                     [originatingService] => Sms
     *                     [latency] => 0
     *                     [parts] => 1
     *                     [price] => 0.006
     *                     [from] => 919600951898
     *                     [direction] => Outbound
     *                 )
     *             [1] => stdClass Object
     *                 (
     *                     [smsId] => ff17fb17-19d8-43ef-a2f4-73135203553e
     *                     [messageId] => 9571ef84-6e34-4601-ba3c-7a5748bc8e7f
     *                     [to] => +919787136232
     *                     [groups] => Array
     *                         (
     *                         )
     *                     [country] => IN
     *                     [operator] => Vodafone
     *                     [status] => stdClass Object
     *                         (
     *                             [status] => Sent
     *                             [date] => 2016-09-19T09:16:54Z
     *                         )
     *                     [body] => Hi Gokul Test Message- stallioni Routee Api
     *                     [campaignName] => API-1474276611
     *                     [applicationName] => default
     *                     [originatingService] => Sms
     *                     [latency] => 0
     *                     [parts] => 1
     *                     [price] => 0.006
     *                     [from] => 919600951898
     *                     [direction] => Outbound
     *                 )
     *             )
     *     [totalPages] => 1
     *     [totalElements] => 2
     *     [last] => 1
     *     [numberOfElements] => 2
     *     [first] => 1
     *     [size] => 20
     *     [number] => 0
     * )
     * </code>
	 */
	 public function usecase3()
	 {
		 
	 }
	/**
	 * Use Case 4: ​I want to create a scheduled campaign and then delete it. Then I want to track the 
	 * campaign and make sure that it doesn’t exist anymore.
	 * 
	 * STEPS | DESCRIPTION
     * ------------ | -------------
	 * 1. | Get authorized
	 * 2. | Create a scheduled campaign for the future
	 * 3. | Using the campaign’s tracking id delete it.
	 * 4. | Track the messages of the campaign. An error should appear saying that the campaign doesn’t exist.
	 * 
	 * @example Code
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * use Routee\lib\Api as api;
	 * $config = array(
	 *     'application-id' => '57b5b7bde4b007f5ba82952b',
	 *     'application-secret' => '6k6sitD5hU',    
	 * );
	 * 
	 * // Step 1:  Get authorized
	 * 
	 * $authResponse = new api\Authorization();
	 * $authResult = $authResponse->getAuthorization($config);
	 * $authResultDecode = json_decode($authResult);
	 * 
	 * // Step 2:  Create a scheduled campaign for the future
	 * 
	 * $data_camp = array(
	 *         'body'=>'Hi [~firstName] Test Message- stallioni Routee Api',        
	 *         'from'=> 'kesav',       
	 *         'contacts'=>array('57df6bd40cf2232979762e18'),        
	 *         'callback' => array(
	 *               "strategy" => "OnChange",
	 *               "url"=>"http://www.yourserver.com/message/callback.php"
	 *             ),
	 *         'flash' => false,
	 *         'smsCallback' => array(
	 *               "strategy" => "OnChange",
	 *                "url"=>"http://www.yourserver.com/message/callback.php"
	 *             ),        
	 *         'campaignName' => 'API-'.time(),
	 *         'to' => array('+917871962432'), 
	 *         'scheduledDate'=>strtotime('+1 days'),     
	 *         'fallbackValues' => array('firstName'=>'Gokul','firstName'=>'Subash'),
	 *     );
	 * 
	 * $sms = new api\Messaging($config);
	 * $sendCampResult = $sms->sendCampaign($data_camp);
	 * $sendCampResultDecode = json_decode($sendCampResult);
	 *
	 * // Step 3: Using the campaign’s tracking id delete it
	 * 
	 * $campaignTrackingId = $sendCampResultDecode->trackingId;
	 * $deleteScheduledCampaignResponse = $sms->deleteScheduledCampaign( $campaignTrackingId );
	 * $deleteScheduledCampaignDecode = json_decode($deleteScheduledCampaignResponse);
	 * 
	 * // Step 4: Track the messages of the campaign to see if they were both delivered
	 * 
	 * $param = array('page'=>'0' );
	 * $campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
	 * $campaignTrackingDecode = json_decode($campaignTrackingResponse);
	 * 
	 * </code>
	 * 
	 * @example Response
	 * 
	 * <code>
	 * 
	 * Step 1: Get authorized - Result
	 * -------------------------------------
	 * stdClass Object
	 * (
	 *     [access_token] => 8029f88d-ce61-4b87-b2be-1260d1b605a0
	 *     [token_type] => bearer
	 *     [expires_in] => 3142
	 *     [scope] => contact sms report account
	 *     [permissions] => Array
	 *         (
	 *             [0] => MT_ROLE_ACCOUNT_FINANCE
	 *             [1] => MT_ROLE_SMS            
	 *             [2] => MT_ROLE_REPORT
	 *             [3] => MT_ROLE_2STEP
	 *             [4] => MT_ROLE_CONTACT
	 *         )
	 * 
	 *  )
	 * 
	 * Step 2: Create a scheduled campaign for the future - Result
	 * -----------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [campaignName] => API-1474277055
	 *     [trackingId] => 88a98a23-00df-448a-91d7-ac5a5363f998
	 *     [type] => Sms
	 *     [state] => Scheduled
	 *     [createdAt] => 2016-09-19T09:24:28.661Z
	 *     [respectQuietHours] => 
	 *     [scheduledDate] => 2016-09-20T09:24:15Z
	 *     [from] => kesav
	 *     [to] => Array
	 *         (
	 *             [0] => +917871962432
	 *         )
	 * 
	 *     [contacts] => Array
	 *         (
	 *             [0] => 57df6bd40cf2232979762e18
	 * 
	 *         )
	 * 
	 *     [body] => Hi [~firstName] Test Message- stallioni Routee Api
	 *     [smsAnalysis] => stdClass Object
	 *         (
	 *             [numberOfRecipients] => 2
	 *             [recipientsPerCountry] => stdClass Object
	 *                 (
	 *                     [IN] => 2
	 *                 )
	 * 
	 *             [recipientCountries] => stdClass Object	 
	 *                (
	 *                     [+917871962432] => IN
	 *                 )
	 * 
	 *             [contacts] => stdClass Object
	 *                 (
	 *                     [57df6bd40cf2232979762e18] => stdClass Object
	 *                         (
	 *                             [recipient] => +919787136232
	 *                             [recipientCountry] => IN
	 *                             [blacklisted] => 
	 *                         )
	 * 
	 * 
	 *                 )
	 *             [recipientsPerGroup] => stdClass Object
	 *                (
	 *                 )
	 *             [totalInGroups] => 0
	 *             [bodyAnalysis] => stdClass Object
	 *                 (
	 *                     [parts] => 1
	 *                     [unicode] =>
	 *                     [characters] => 50
	 *                )
	 *         )
	 *     [flash] => 
	 *     [fallbackValues] => stdClass Object
	 *         (
	 *             [firstName] => Subash
	 *         )
	 *     [statuses] => stdClass Object
	 *         (
	 *             [Queued] => 0
	 *             [Sent] => 0
	 *             [Failed] => 0
	 *             [Delivered] => 0
	 *             [Undelivered] => 0
	 *         )
	 *     [callback] => stdClass Object
	 *         (
	 *             [url] => http://www.yourserver.com/message/callback.php
	 *             [strategy] => OnChange
	 *         )
	 *     [cost] => 0
	 *     [totalMessages] => 2
	 * )
	 * 
	 * Step 3: Using the campaign’s tracking id delete it - Result
	 * ------------------------------------------------------------
	 * 
	 * 
	 * Step 4: Track the messages of the campaign to see if they were both delivered - Result
	 * --------------------------------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [code] => 404009001
	 *     [developerMessage] => campaign with trackingId '88a98a23-00df-448a-91d7-ac5a5363f998' was not found!
	 *     [entity] => campaign
	 *     [property] => trackingId
	 *     [value] => 88a98a23-00df-448a-91d7-ac5a5363f998
	 * )
	 * 
	 * </code>
	 * 
	 */
	 public function usecase4()
	 {
		 
	 }	 
	/**
	 * Use Case5:I want to get authorized with my application credentials,send a two-step verification,
	 * see its status,invalidate it and check that its status is now "Cancelled".
	 * 
	 * STEPS | DESCRIPTION
     * ------------ | -------------
	 * 1. | Get authorized with application credentials ( make sure your application has "Two-step Verification" service permissions)
	 * 2. | Send a two step verification to your mobile with a 5-digit code
	 * 3. | Using the verification's id view its status.It should be "Pending"
	 * 4. | Using the verification's id invalidate it.
	 * 5. | Get the code from your phone and try to confirm it.You should get an error.
	 * 6. | Using the verification's id view its status.It should be "Cancelled".
	 * @example Code
	 * <code>
	 * require_once __DIR__ . '/vendor/autoload.php';
	 * use Routee\lib\Api as api;
	 * $config = array(
	 *     'application-id' => '57bd7450e4b07bf187df66ed',
	 *     'application-secret' => 'tC1XhTGae4'
	 * );
	 * 
	 * // Step 1:  Get authorized with application credentials (make sure your application has "Two­StepVerification" service permissions)
	 * $authResponse = new api\Authorization();
	 * $authResult = $authResponse->getAuthorization($config);
	 * $authResultDecode = json_decode($authResult);
	 * $premissions = $authResultDecode->permissions;
	 * 
	 * // Step 2:  Send a two step verification to your mobile with a 5­digit code
	 * if(in_array('MT_ROLE_2STEP',$premissions))
	 * {
	 *     $twostep = api\TwoStep::getInstance( $config );
	 *     $data = array(
	 *         'method' => 'sms',
	 *         'type'   => 'code',
	 *         'recipient'   => '+917871962432'
	 *     );
	 *     $twostepResponse = $twostep->start2StepVerification($data);
	 *     $twostepResponseDecode = json_decode($twostepResponse);
	 * }
	 * 
	 * // Step 3: Using the verification’s id view its status. It should be "Pending"
	 * if(isset($twostepResponseDecode) && $twostepResponseDecode->status == 'Pending')
	 * {
	 *     echo 'True';echo '<br>';
	 * }
	 * 
	 * $smsTrackid = $twostepResponseDecode->trackingId;
	 * 
	 * // Step 4: Using the verification's id invalidate it.
	 * for($i=1; $i<=5;$i++){
	 *     $verify_data = array('answer'=>'6036');
	 *     $verificationResult = $twostep->confirm2StepStatus($verify_data,$smsTrackid);
	 *     $verificationResultDecodes = json_decode($verificationResult);
	 *     echo '<pre>';print_r($verificationResultDecodes);echo '</pre>';
	 * }
	 * 
	 * // Step 5: Get the code from your phone and try to confirm it. You should get an error
	 * 
	 * $verify_data = array('answer'=>'2622');
	 * $verificationResult = $twostep->confirm2StepStatus($verify_data,$smsTrackid);
	 * $verificationResultDecode = json_decode($verificationResult);
	 * 
	 * if(isset($verificationResultDecode) && $verificationResultDecode->developerMessage !='')
	 * {
	 *   echo $verificationResultDecode->developerMessage; echo '<br>';
	 * }
	 * 
	 * 
	 * // Step 6: Using the verification’s id view its status. It should be "Failed".
	 * 
	 * $retrieveResult = $twostep->retrieve2StepStatus($smsTrackid);
	 * $retrieveResultDecode = json_decode($retrieveResult);
	 * 
	 * echo '<pre>'; print_r($retrieveResultDecode); echo '</pre>';
	 * 
	 * </code>
	 * 
	 * @example Response
	 * <code>
	 * 
	 * Step 1: Get authorized - Result
	 * --------------------------------
	 * stdClass Object
	 * (
	 *     [access_token] => 169f7e17-fd84-46e7-a808-0f2f70286b2b
	 *     [token_type] => bearer
	 *     [expires_in] => 3599
	 *     [scope] => contact 2step sms report account
	 *     [permissions] => Array
	 *         (
	 *             [0] => MT_ROLE_ACCOUNT_FINANCE
	 *             [1] => MT_ROLE_SMS
	 *             [2] => MT_ROLE_REPORT
	 *             [3] => MT_ROLE_2STEP
	 *             [4] => MT_ROLE_CONTACT
	 * 
	 * 
	 *         )
	 * )
	 * 
	 * Step 1 a) : Listout Permissions - Result
	 * -----------------------------------------
	 * Array
	 * (
	 * 
	 *     [0] => MT_ROLE_ACCOUNT_FINANCE
	 *     [1] => MT_ROLE_SMS
	 *     [2] => MT_ROLE_REPORT
	 *     [3] => MT_ROLE_2STEP
	 *     [4] => MT_ROLE_CONTACT
	 * )
	 * 
	 * 
	 * Step 2: Send a two step verification to your mobile with a 5­digit code - Result
	 * -------------------------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [trackingId] => 0ac530cb-5d48-4bac-9f10-61860374f213
	 *     [status] => Pending
	 *     [updatedAt] => 2016-09-19T09:29:39.155Z
	 * )
	 * 
	 * Step 3: Using the verification's id view its status. It should be "Pending" - Result
	 * ------------------------------------------------------------------------------------
	 * 
	 * True
	 * 
	 * Step 4: Using the verification's id invalidate it - Result
	 * ----------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [code] => 400011005
	 *     [developerMessage] => Wrong Answer!
	 * )
	 * stdClass Object
	 * (
	 *     [code] => 400011005
	 *     [developerMessage] => Wrong Answer!
	 * )
	 * stdClass Object
	 * (   
	 *     [code] => 400011005
	 *     [developerMessage] => Wrong Answer!
	 * )
	 * stdClass Object
	 * (
	 *     [code] => 400011005
	 *     [developerMessage] => Wrong Answer!
	 * )
	 * stdClass Object
	 * (
	 *     [code] => 400011002    
	 *     [developerMessage] => Invalid Status
	 * 
	 * )
	 * 
	 * Step 4: Get the code from your phone and try to confirm it. You should get an error- Result
	 * -------------------------------------------------------------------------------------------
	 * Invalid Status
	 * 
	 * Step 6: Using the verification’s id view its status. It should be "Failed" - Result
	 * -----------------------------------------------------------------------------------
	 * stdClass Object
	 * (
	 *     [trackingId] => 0ac530cb-5d48-4bac-9f10-61860374f213
	 *     [status] => Failed
	 *     [updatedAt] => 2016-09-19T09:29:44.223Z
	 * 
	 * )
	 * 
	 * </code>
	 */
	 public function usecase5()
	 {
		 
	 }	 
}
