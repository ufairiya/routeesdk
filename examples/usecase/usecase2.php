<?php
/* Usecase2.php */

require_once  '../../vendor/autoload.php';

use Routee\lib\Api as api;

$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',    
);

// Step 1:  Get authorized
$authResponse = new api\Authorization();
$authResult = $authResponse->getAuthorization($config);
$authResultDecode = json_decode($authResult);

echo 'Step 1: Get authorized - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($authResultDecode);echo '</pre>';echo '<br>';

// Step 2:  Send a single SMS to a mobile number and “To”:”amdTelecom”
$sms = new api\Messaging($config);
    $data_sms = array(
        'body'=>'Test Message- AMDTelcom Routee Api',
        'to'=> 'amdTelecom',
        'from'=> 'kesav',
        'flash'=> false,
        'label'=>'AMDTelcom Routee',
        'callback' => array(
            'strategy' => 'OnChange',
            'url' => 'http://stallioni.in/470-routee/callback.php',
            )
        );
$sendSmsResult = $sms->sendSingleSMS($data_sms);
$sendSmsResultDecode = json_decode($sendSmsResult);

echo 'Step 2 a): Send a single SMS to a mobile number and "To":"amdTelecom" - Result'; echo '<br>';
echo '----------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($sendSmsResultDecode);echo '</pre>';echo '<br>';

$data_sms_success = array(
        'body'=>'Test Message- AMDTelcom Routee Api',
        'to'=> '919787136232',
        'from'=> 'kesav',
        'flash'=> false,
        'label'=>'AMDTelcom Routee',
        'callback' => array(
            'strategy' => 'OnChange',
            'url' => 'http://stallioni.in/470-routee/callback.php',
            )
);
$sendSmsSuccessResult = $sms->sendSingleSMS($data_sms_success);
$sendSmsSuccessResultDecode = json_decode($sendSmsSuccessResult);

echo 'Step 2 b): Send a single SMS to a mobile number and "To":"919787136232" - Result'; echo '<br>';
echo '----------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($sendSmsSuccessResultDecode);echo '</pre>';echo '<br>';

//Step 3:  Track your messages with filters and use fieldName: "To" and "searchTerm":"amdTelecom"

$data_filter = array(
                    'filter_param' => array(
                        array(
                            'fieldName'  => 'to',
                            'searchTerm' => 'amdTelecom'
                            )
                        ),
                    
        );
$SMSfilterResponse = $sms->filterMultipleSMS( $data_filter );
$SMSfilterResponseDecode = json_decode($SMSfilterResponse);

echo 'Step 3 a): Track your messages with filters and use fieldName: "To" and "searchTerm":"amdTelecom" - Result'; echo '<br>';
echo '-------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($SMSfilterResponseDecode);echo '</pre>';echo '<br>';


$data_filters = array(
                    'filter_param' => array(
                        array(
                            'fieldName'  => 'to',
                            'searchTerm' => '+917871962432'
                            )
                        ),
                    
        );
$SMSfilterResponseB = $sms->filterMultipleSMS( $data_filters );
$SMSfilterResponseBDecode = json_decode($SMSfilterResponseB);

echo 'Step 3 b): Track your messages with filters and use fieldName: "To" and "searchTerm":"917871962432" - Result'; echo '<br>';
echo '-------------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($SMSfilterResponseBDecode);echo '</pre>';echo '<br>';


?>