<?php
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;

$sms = new api\Messaging($config);
$data_camp = array(
        'body'=>'Hi [~firstName] Test Message- stallioni Routee Api',        
        'from'=> '919600951898',       
        'reminder' => array(
            'minutesAfter'=> '5',
            'minutesBefore'=> '5',
            'to' => array('+919787136232'),
            ),
        'callback' => array(
              "strategy" => "OnChange",
              "url"=>"http://localhost/routee/test.php"
            ),
        'flash' => false,
        'smsCallback' => array(
              "strategy" => "OnChange",
              "url"=>"http://localhost/routee/test.php"
            ),
        // 'scheduledDate'=>strtotime('+30 days'),
        'campaignName' => 'API-'.time(),       
        'to' => array('+919787136232'),
        'fallbackValues' => array('firstName'=>'Gokul'),
    );
$send_camp = $sms->sendCampaign($data_camp);
print_r($send_camp);
?>