<?php
/* RETRIEVE THE COUNTRIES THAT ARE SUPPORTED BY THE QUIET HOURS FEATURE */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$countryID = 'en';
$retriveCountriesResponse = $sms->retriveCountriesQuietHour( $countryID );
print_r( $retriveCountriesResponse );
?>