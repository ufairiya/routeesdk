<?php
/* AuthenticateTest.php */
require_once  '../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',
    // 'scope' => 'account contact report sms'
);
//$config = array();
use Routee\lib\Api as auth;
$authResponse = new auth\Authorization();
$authResult = $authResponse->getAuthorization($config);
echo $authResult;

//{"timestamp":1472894732599,"status":401,"error":"Unauthorized","message":"Bad credentials","path":"/oauth/token"}
?>