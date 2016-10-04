<?php
/* VIEW VOLUME/PRICE SUMMARY ANALYTICS FOR A CAMPAIGN */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$reports = api\Reports::getInstance( $config );
$data = array(
	'offset' => '+02:00', /* A time-zone offset from Greenwich/UTC, such as +02:00. */
	'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5' /* The id of the campaign that the messages belong to. */
	);
echo '<pre>';
print_r( json_decode($reports->viewCampaignAnalytics( $data )) );
echo '</pre>';
?>