<?php
/* RETRIEVE THE AVAILABLE BANK ACCOUNTS */
require_once  '../../vendor/autoload.php';
use Routee\lib\Api as api;
$account = api\Accounts::getInstance();
echo '<pre>';
print_r( $account->retrieveAvailBankAccounts() );
echo '</pre>';
?>