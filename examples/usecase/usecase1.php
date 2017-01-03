<?php
/* Usecase1.php */

require_once  '../../vendor/autoload.php';

use Routee\lib\Api as api;

$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU',    
);

// Step 1:  Get authorized
$authResponse = new api\Authorization();
$authResult = $authResponse->getAuthorization($config);
$authResultDecode = json_decode($authResult);

echo 'Step 1: Get authorized - Result'; echo '<br>';
echo '-------------------------------------';echo '<br>';
echo '<pre>';print_r($authResultDecode);echo '</pre>';echo '<br>';


// Step 2:  Create a custom numeric label named "cats"
$data = array(
	array(
       'name' => 'cats',
       'type' => 'Number'
	)
);
$contactResponse = new api\Contacts($config);
$contactlabelResult = $contactResponse->createLabel($data);
$ContactlabelDecode = json_decode($contactlabelResult);

echo 'Step 2: Create a custom numeric label named "cats" - Result'; echo '<br>';
echo '------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($ContactlabelDecode);echo '</pre>';echo '<br>';

// Step 3:  Create a custom text label named "address"
$data_group_address = array(
	array(
       'name' => 'address',
       'type' => 'Text'
	)
);
$contactlabelAddrResult = $contactResponse->createLabel($data_group_address);
$contactlabelAddrDecode = json_decode($contactlabelAddrResult);

echo 'Step 3: Create a custom text label named "address" -  Result'; echo '<br>';
echo '--------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($contactlabelAddrDecode);echo '</pre>';echo '<br>';

// Step 4:  Create a new contact that has 5 cats and lives at “Some Address”
$data_contact = array(
	           'firstName' => 'kesava',
	           'lastName' => 'moorthi m',
	           'mobile' => '+919025060261',
	           'vip' => 'false',
	           'labels'=> array(
		           	 array(
		           	 	'name' => 'cats',
		           	 	'type' => 'Number',	           	    
		             	'value' => 5,
		           	 ),
		           	 array(
		           	 	'name' => 'address',
		           	    'value' => '123456,annur,coimbatore-641653',
		           	 ),
	           	)
);

$contactResult = $contactResponse->createContacts( $data_contact );
$contactDecode = json_decode($contactResult);

echo 'Step 4: Create a new contact that has 5 cats and lives at "Some Address" - Result'; echo '<br>';
echo '----------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($contactDecode);echo '</pre>';echo '<br>';

// Step 5:  Create a new group called "PeopleWithCats"
$group_name = 'PeopleWithCats';
$data_group = array(
    'name' => $group_name,         
);
$contactGroupResult = $contactResponse->createGroup($data_group);
$contactGroupDecode = json_decode($contactGroupResult);

echo 'Step 5: Create a new group called "PeopleWithCats" - Result'; echo '<br>';
echo '------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($contactGroupDecode);echo '</pre>';echo '<br>';

// Step 6: Add the contact to the group
$data_group_name = array($contactDecode->id);
$contactGroupAddResult = $contactResponse->addContactsToGroupByName($data_group_name,$group_name);
$contactGroupContactDecode = json_decode($contactGroupAddResult);


echo 'Step 6: Add the contact to the group - Result'; echo '<br>';
echo '-----------------------------------------------------';echo '<br>';
echo '<pre>';print_r($contactGroupContactDecode);echo '</pre>';echo '<br>';

// Step 7:  Retrieve the details of the contact to see if the contact has the group attached to it
$contactSingleResult = $contactResponse->retrieveSingleContacts($contactDecode->id );
$contactSingleResultDecode = json_decode($contactSingleResult);

echo 'Step 7: Retrieve the details of the contact to see if the contact has the group attached to it - Result'; echo '<br>';
echo '-------------------------------------------------------------------------------------------------------------';echo '<br>';
echo '<pre>';print_r($contactSingleResultDecode);echo '</pre>';echo '<br>';

?>