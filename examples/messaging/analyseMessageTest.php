<?php
/* Analyse a Campaign. */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$sms = new api\Messaging($config);
    $analyseCampData = array(
        'from'  =>  '919600951898',
        'to'    =>  array(
                    '919787136232'
                    ),
        'body'  =>  'Test Message- stallioni Routee Api',
        'groups' => array(
                    1
                    )
        );
$analyseCampResponse = $sms->analyzeCampaignSMS( $analyseCampData );
print_r( $analyseCampResponse );
?>