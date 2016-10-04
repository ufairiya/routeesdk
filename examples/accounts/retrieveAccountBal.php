<?php
/* RETRIEVE THE BALANCE OF YOUR ACCOUNT */
require_once  '../../vendor/autoload.php';
use Routee\lib\Api as api;
$account = api\Accounts::getInstance();
echo '<pre>';
// print_r( $account );
echo '</pre>';
echo '<pre>';
print_r( $account->retrieveAccountBal() );
echo '</pre>';
?>