<?php
/* ADD CONTACTS TO A SPECIFIED GROUP */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$group_name = 'AMD Telecom';
$data = array('580aef600cf2ea5b5f2d472c');
$contactResult = $contactResponse->addContactsToGroupByName($data,$group_name);
print_r( $contactResult );
?>