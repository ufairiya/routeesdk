<?php
/* Delete Single contact */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
);

use Routee\lib\Api as api;
$contactResponse = new api\Contacts($config);
$contactid = '57bc36470cf22cec5c422c9b';
$contactResult = $contactResponse->deleteContact($contactid );
echo '<pre>';
print_r($contactResult);
?>