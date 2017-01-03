<?php
/* RETRIEVE THE AVAILABLE BANK ACCOUNTS */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',    
);
use Routee\lib\Api as api;
$account = api\Accounts::getInstance($config);
echo '<pre>';
print_r( $account->retrieveAvailBankAccounts() );
echo '</pre>';
?>