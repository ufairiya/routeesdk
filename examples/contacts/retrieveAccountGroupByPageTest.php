<?php
/* RETRIEVE THE ACCOUNT’S GROUPS IN PAGED FORMAT */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$data_page = array(
         'page' => '0',
         'size' => '3',
	);
$contactResult = $contactResponse->retrieveAccountGroupByPage($data_page);
print_r( $contactResult );
?>