<?php
/* Delete Multiple contact */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;

$data_del = array(
    array(
        'id' => '57b707690cf2121b4951e8e9'
         ),
    array(
        'id' => '57b703ea0cf2121b4951e8e2'
         ),
	);
$contactResponse = new api\Contacts($config);
$contactResult = $contactResponse->deleteMultipleContacts($data_del );
echo '<pre>';
print_r($contactResult);
?>