<?php
/* CREATE A NEW GROUP */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$data = array(
         'name' => 'TestGroup'.rand(0000,9999),         
	);
$contactResult = $contactResponse->createGroup($data);
print_r( $contactResult );
?>