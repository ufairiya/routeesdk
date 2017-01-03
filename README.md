## Installation 

1) Extract the zip file.

2) Insert the following line of code at the top of your application.
require_once __DIR__ . '/vendor/autoload.php';

3) Refer the Examples in the examples folder for sample implementation.


## Routee SDK Configuration 
Add your application-id, application-secret at every Api call

$Config = array(
        'application-id' => 'enter your application-id',
        'application-secret' => 'enter your application-secret',
        'scope'  => 'string'
    );

Please refer example file: examples/authenticateTest.php


## SDK Documentation
/doc - This folder contain the documentation about Routee-SDK.

# Routee SDK 
/src - This folder contain the SDK PHP files for Routee API

PATH : /src/Routee/lib/Api

  1) Authentication API Call 
  2) Messaging API Call
  3) Contacts API Call
  4) Accounts API Call
  5) 2step verification API Call
  6) Reports API Call

# Routee SDK Examples
/examples - This folder contain the examples files for the following Routee API call
  1) Authentication API Call 
  2) Messaging API Call
  3) Contacts API Call
  4) Accounts API Call
  5) 2step verification API Call
  6) Reports API Call

# Unit Test 
 /test - This folder contain the unit test for the Routee API Calls.

