<?php
/* RETRIEVE DETAILS FOR A CAMPAIGN */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$retrieveCampID = '0b978991-2b39-4b8b-874a-e449e792e020';
$retrieveCampaignResponse = $sms->retrieveDetailsCampaign( $retrieveCampID );
echo '<pre>';
print_r( json_decode($retrieveCampaignResponse) );
echo '</pre>';
?>