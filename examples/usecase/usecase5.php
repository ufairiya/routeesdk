<?php
/* Usecase5.php */

require_once  '../../vendor/autoload.php';

use Routee\lib\Api as api;

$config = array(
    'application-id' => '57bd7450e4b07bf187df66ed',
    'application-secret' => 'tC1XhTGae4'
);

// Step 1:  Get authorized with application credentials (make sure your application has "Two­StepVerification" service permissions)
$authResponse = new api\Authorization();
$authResult = $authResponse->getAuthorization($config);
$authResultDecode = json_decode($authResult);
$premissions = $authResultDecode->permissions;

echo 'Step 1: Get authorized - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($authResultDecode);echo '</pre>';echo '<br>';


echo 'Step 1 a) : Listout Permissions - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($premissions);echo '</pre>';echo '<br>';

// Step 2:  Send a two step verification to your mobile with a 5­digit code
if(in_array('MT_ROLE_2STEP',$premissions))
{
    $twostep = api\TwoStep::getInstance( $config );
    $data = array(
        'method' => 'sms',
        'type'   => 'code',
        'recipient'   => '+917871962432'
    );
    $twostepResponse = $twostep->start2StepVerification($data);
    $twostepResponseDecode = json_decode($twostepResponse);
}

echo 'Step 2: Send a two step verification to your mobile with a 5­digit code - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($twostepResponseDecode);echo '</pre>';echo '<br>';

// Step 3: Using the verification’s id view its status. It should be "Pending"
if(isset($twostepResponseDecode) && $twostepResponseDecode->status == 'Pending')
{
 
    echo 'Step 3: Using the verification’s id view its status. It should be "Pending" - Result'; echo '<br>';
    echo '-------------------------------------';echo '<br>';
    echo '<pre>';echo 'True';echo '</pre>';echo '<br>';

}


echo 'Step 4: Using the verification\'s id invalidate it - Result'; echo '<br>';
echo '----------------------------------------------------------------';echo '<br>';

$smsTrackid = $twostepResponseDecode->trackingId;

// Step 4: Using the verification's id invalidate it.
for($i=1; $i<=5;$i++){
    $verify_data = array('answer'=>'6036');
    $verificationResult = $twostep->confirm2StepStatus($verify_data,$smsTrackid);
    $verificationResultDecodes = json_decode($verificationResult);
    echo '<pre>';print_r($verificationResultDecodes);echo '</pre>';
}

echo 'Step 4:  Get the code from your phone and try to confirm it. You should get an error- Result'; echo '<br>';
echo '----------------------------------------------------------------';echo '<br>';
// Step 5: Get the code from your phone and try to confirm it. You should get an error

$verify_data = array('answer'=>'2622');
$verificationResult = $twostep->confirm2StepStatus($verify_data,$smsTrackid);
$verificationResultDecode = json_decode($verificationResult);

if(isset($verificationResultDecode) && $verificationResultDecode->developerMessage !='')
{
  echo $verificationResultDecode->developerMessage; echo '<br>';
}


// Step 6: Using the verification’s id view its status. It should be "Failed".

$retrieveResult = $twostep->retrieve2StepStatus($smsTrackid);
$retrieveResultDecode = json_decode($retrieveResult);

echo 'Step 6: Using the verification’s id view its status. It should be "Failed" - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($retrieveResultDecode);echo '</pre>';echo '<br>';

?>