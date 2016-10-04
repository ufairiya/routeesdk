<?php
/* TRACK MULTIPLE SMS OF A SPECIFIC CAMPAIGN */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$campaignTrackingId = '125e05df-0dc9-41de-b3f7-4e0f71fe4f04';
$param = array('page'=>'0' );
$campaignTrackingResponse = $sms->trackCampaignMultiSMS( $campaignTrackingId,$param );
print_r( $campaignTrackingResponse );
?>