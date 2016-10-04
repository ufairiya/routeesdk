<?php
/* Add contacts to black list */
require_once  '../../vendor/autoload.php';

use Routee\lib\Api as api;
$contactResponse = new api\Contacts();
$data_service =  array(
	array(
	'id'     => '57c580380cf2d47a564ae51c',),
	);
$service = 'Sms';
$contactResult = $contactResponse->addContactToBlackLists( $data_service,$service );
print_r( $contactResult );
?>