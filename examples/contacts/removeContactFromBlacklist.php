<?php
/* REMOVE A GROUP OF CONTACTS FROM THE BLACKLIST */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$data =  array('testGroup');
$service = 'Sms';
$contactResult = $contactResponse->removeGroupOfContactFromBlackList( $data,$service );
print_r( $contactResult );
?>