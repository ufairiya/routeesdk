<?php
/* Create a new contact */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
// {
//    "labels":[
//       {
//          "name":"string",
//          "value":"string"
//       }
//    ],
//    "email":"string",
//    "firstName":"string",
//    "lastName":"string",
//    "mobile":"string",
//    "vip": "boolean"
// }
$data_contact = array(
           'firstName' => 'kesava',
           'lastName' => 'moorthi',
           'mobile' => '+919025060261',
           'vip' => 'true',
	);

$contactResult = $contactResponse->createContacts( $data_contact );
print_r( $contactResult );
?>