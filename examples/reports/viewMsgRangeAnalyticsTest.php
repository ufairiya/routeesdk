<?php
/* VIEW TIME SUMMARY ANALYTICS FOR A RANGE OF MESSAGES */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$reports = api\Reports::getInstance( $config );
$data = array(
	'startDate' => '2015-01-01T00:00:00.000Z',
	'endDate' => '2017-01-01T00:00:00.000Z'
	);
echo '<pre>';
print_r( json_decode($reports->viewMsgRangeAnalytics( $data )) );
echo '</pre>';
?>