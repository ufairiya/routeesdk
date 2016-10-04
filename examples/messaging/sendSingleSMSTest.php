<?php
/* Examples/sendSingleSMSTest.php */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$sms = new api\Messaging($config);
    $data_sms = array(
        'body'=>'Test Message- stallioni Routee Api',
        'to'=> '919787136232',
        'from'=> '919600951898',
        'flash'=> false,
        'label'=>'stallioni Routee',
        'callback' => array(
            'strategy' => 'OnChange',
            'url' => 'http://stallioni.in/470-routee/callback.php',
            )
        );
$send_sms = $sms->sendSingleSMS($data_sms);
print_r($send_sms);
?>