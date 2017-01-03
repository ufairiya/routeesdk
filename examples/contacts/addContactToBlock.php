<?php
/* Add contacts to black list */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',    
);
use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$data_service =  array(
	array(
	'id'     => '580aef600cf2ea5b5f2d472c',),
	);
$service = 'Sms';
$contactResult = $contactResponse->addContactToBlackLists( $data_service,$service );
print_r( $contactResult );
?>