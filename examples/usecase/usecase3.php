<?php
/* Usecase3.php */

require_once  '../../vendor/autoload.php';

use Routee\lib\Api as api;

$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',    
);

// Step 1:  Get authorized
$authResponse = new api\Authorization();
$authResult = $authResponse->getAuthorization($config);
$authResultDecode = json_decode($authResult);

echo 'Step 1: Get authorized - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($authResultDecode);echo '</pre>';echo '<br>';

// Step 2:  Create a contact

$data_contact = array(
               'firstName' => 'Gokul',
               'lastName' => 'kumar statllioni',
               'mobile' => '+919787136232',
               'vip' => 'false',               
               
);
$contactResponse = new api\Contacts($config);
$contactResult = $contactResponse->createContacts( $data_contact );
$contactDecode = json_decode($contactResult);

echo 'Step 2: Create a contact - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($contactDecode);echo '</pre>';echo '<br>';

// Step 3:  Send a campaign to this contact and an additional recipient. Make sure you add campaign callback details

$data_camp = array(
        'body'=>'Hi [~firstName] Test Message- stallioni Routee Api',        
        'from'=> '919600951898',       
        'contacts'=>array($contactDecode->id),        
        'callback' => array(
              "strategy" => "OnChange",
              "url"=>"http://stallioni.in/470-routee/callback.php"
            ),
        'flash' => false,
        'smsCallback' => array(
              "strategy" => "OnChange",
              "url"=>"http://stallioni.in/470-routee/callback.php"
            ),        
        'campaignName' => 'API-'.time(),
        'to' => array('+917871962432'),      
        'fallbackValues' => array('firstName'=>'Gokul','firstName'=>'Subash'),
    );
$sms = new api\Messaging($config);
$sendCampResult = $sms->sendCampaign($data_camp);
$sendCampResultDecode = json_decode($sendCampResult);

echo 'Step 3: Send a campaign to this contact and an additional recipient. Make sure you add campaign callback details - Result'; echo '<br>';
echo '------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($sendCampResultDecode);echo '</pre>';echo '<br>';

// Step 4: Make sure you receive campaign callback for the campaign you just sent.
/*
callback.php
------------
$webhookContent = '';
$webhook = fopen('php://input' , 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook);

echo $webhookContent;


*/

echo 'Step 4: campaign callback  - Result'; echo '<br>';
echo '------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';echo '{"messageId":"28969a49-7c12-4411-a93b-0d3751b4aca0","campaignTrackingId":"383d7d67-25de-48f7-b16f-50683894bbdd","to":"+917871962432","from":"919600951898","country":"IN","operator":"Airtel (Bharti Airtel)","groups":[],"campaignName":"API-1474265064","status":{"name":"Delivered","updatedDate":"2016-09-19T06:04:32Z"},"message":"Hi Subash Test Message- stallioni Routee Api","applicationName":"default","latency":5,"parts":1,"price":0.00600000,"direction":"Outbound","originatingService":"Sms"}';echo '</pre>';echo '<br>';

// Step 5: Retrieve the details of the campaign (use the https://connect.routee.net/campaigns/{trackingId} endpoint)
// Step 6: Track the messages of the campaign to see if they were both delivered
$campaignTrackingId = $sendCampResultDecode->trackingId;
$param = array('page'=>'0' );
$campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
$campaignTrackingDecode = json_decode($campaignTrackingResponse);

echo 'Step 5:  Retrieve the details of the campaign - Result'; echo '<br>';
echo '------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($campaignTrackingDecode);echo '</pre>';echo '<br>';


echo 'Step 5: Track the messages of the campaign to see if they were both delivered - Result'; echo '<br>';
echo '------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($campaignTrackingDecode);echo '</pre>';echo '<br>';



?>