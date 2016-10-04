<?php
/* TRACK A SINGLE SMS */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$sms = new api\Messaging($config);
$messageID = 'f5b6f428-90a5-46f1-9e3f-c2f170cbe539';
$trackSMSResponse = $sms->trackSingleSMSbyId( $messageID );
print_r( $trackSMSResponse );
?>