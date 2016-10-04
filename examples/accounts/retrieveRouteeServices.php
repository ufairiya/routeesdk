<?php
/* RETRIEVE THE PRICES FOR ALL ROUTEE SERVICES */
require_once  '../../vendor/autoload.php';
use Routee\lib\Api as api;
$account = api\Accounts::getInstance();
echo '<pre>';
// print_r( $account );
echo '</pre>';
echo '<pre>';
print_r( $account->retrieveRouteeServices() );
echo '</pre>';
?>