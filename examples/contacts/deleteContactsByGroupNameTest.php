<?php
/* DELETE CONTACTS OF A SPECIFIED GROUP */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$group_name = 'one';
$data = array('57bc372f0cf22cec5c422c9c');
$contactResult = $contactResponse->deleteContactsByGroupName($data,$group_name);
print_r( $contactResult );
?>