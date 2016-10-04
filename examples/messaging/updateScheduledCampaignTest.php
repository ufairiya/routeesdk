<?php
/* UPDATE A SCHEDULED MESSAGE CAMPAIGN */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$updateData = array(
    'from' => '919443661223',
    'callback' => array(
            'url' => 'http://localhost/routee/test-03.php'
        ),
    'contacts' => array( ),
    'fallbackValues' => array(
            'firstName' => 'Nandha Kumar Viswanaathan'
        ),
    'flash' => true,
    'groups' => array( ),
    'body' => 'Hi! This is a updated Campaign message 03.',
    'campaignName' => 'Campaign-Update-1471945828',
    'to' => array(
            '0' => '+919443661113'
        ),
    'respectQuietHours' => true,
    'scheduledDate' => '2016-09-20T09:26:53Z'
);
$updateScheduledCampaignResponse = $sms->updateScheduledCampaign( $updateData, 'a23e8ffc-f606-49aa-98c4-d46a83ed41f1' );
echo '<pre>';
print_r( json_decode($updateScheduledCampaignResponse) );
echo '</pre>';
?>