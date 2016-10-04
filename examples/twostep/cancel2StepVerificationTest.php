<?php
/* CANCELLING A 2STEP VERIFICATION */
require_once  '../../vendor/autoload.php';
$config = array(
	'application-id' => '57bd7450e4b07bf187df66ed',
	'application-secret' => 'tC1XhTGae4'
    );
use Routee\lib\Api as api;
$twostep = api\TwoStep::getInstance( $config );
echo '<pre>';
print_r( json_decode( $twostep->cancel2StepStatus('1b5df1e8-3701-41e5-9c41-00904532b656') ) );
echo '</pre>';
?>