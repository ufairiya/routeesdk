<?php
/* Usecase4.php */

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

// Step 2:  Create a scheduled campaign for the future

$data_camp = array(
        'body'=>'Hi [~firstName] Test Message- stallioni Routee Api',        
        'from'=> 'kesav',       
        'contacts'=>array('57df6bd40cf2232979762e18'),        
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
        'scheduledDate'=>strtotime('+1 days'),     
        'fallbackValues' => array('firstName'=>'Gokul','firstName'=>'Subash'),
    );
$sms = new api\Messaging($config);
$sendCampResult = $sms->sendCampaign($data_camp);
$sendCampResultDecode = json_decode($sendCampResult);


echo 'Step 2: Create a scheduled campaign for the future - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($sendCampResultDecode);echo '</pre>';echo '<br>';

// Step 3: Using the campaign’s tracking id delete it

$campaignTrackingId = $sendCampResultDecode->trackingId;
$deleteScheduledCampaignResponse = $sms->deleteScheduledCampaign( $campaignTrackingId );
$deleteScheduledCampaignDecode = json_decode($deleteScheduledCampaignResponse);

echo 'Step 3: Using the campaign’s tracking id delete it - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($deleteScheduledCampaignDecode);echo '</pre>';echo '<br>';

// Step 4: Track the messages of the campaign to see if they were both delivered

$param = array('page'=>'0' );
$campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
$campaignTrackingDecode = json_decode($campaignTrackingResponse);


echo 'Step 4: Track the messages of the campaign to see if they were both delivered - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($campaignTrackingDecode);echo '</pre>';echo '<br>';


?>