<?php
/* RETRIEVING THE STATUS OF A 2STEP VERIFICATION */
require_once  '../../vendor/autoload.php';
$config = array(
	'application-id' => '57bd7450e4b07bf187df66ed',
	'application-secret' => 'tC1XhTGae4'
    );
use Routee\lib\Api as api;
$twostep = api\TwoStep::getInstance( $config );
echo '<pre>';
print_r( json_decode( $twostep->retrieve2StepStatus('52040307-2179-49da-8291-83bbfd4ac4d3') ) );
echo '</pre>';
?>