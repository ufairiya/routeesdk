<?php
/* SENDING A 2STEP VERIFICATION */
require_once  '../../vendor/autoload.php';
$config = array(
	'application-id' => '57bd7450e4b07bf187df66ed',
	'application-secret' => 'tC1XhTGae4'
    );
use Routee\lib\Api as api;
$twostep = api\TwoStep::getInstance( $config );
$data = array(
     'method' => 'sms',
     'type'   => 'code',
     'recipient'   => '+919600951898'
	);
echo '<pre>';
print_r( json_decode( $twostep->start2StepVerification($data) ) );
echo '</pre>';
?>