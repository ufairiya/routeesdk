<?php
/* REMOVE CONTACTS FROM BLACKLIST */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$service_name = 'Sms';
$data = array(array('id'=>'57c580380cf2d47a564ae51c'));
$contactResult = $contactResponse->removeContactsFromBlacklists($data,$service_name);
print_r( $contactResult );
?>