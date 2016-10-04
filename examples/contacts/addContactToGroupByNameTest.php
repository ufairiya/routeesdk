<?php
/* ADD CONTACTS TO A SPECIFIED GROUP */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$group_name = 'Stallioni';
$data = array('57c3dc0a0cf2d47a564a2af1');
$contactResult = $contactResponse->addContactsToGroupByName($data,$group_name);
print_r( $contactResult );
?>