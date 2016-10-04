<?php
/* DELETE A SCHEDULED CAMPAIGN */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$delCampTrID = 'a23e8ffc-f606-49aa-98c4-d46a83ed41f1';
$deleteScheduledCampaignResponse = $sms->deleteScheduledCampaign( $delCampTrID );
echo '<pre>';
print_r( json_decode($deleteScheduledCampaignResponse) );
echo '</pre>';
?>