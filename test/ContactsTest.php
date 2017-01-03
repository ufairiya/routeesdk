<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as api;

class ContactsTest extends TestCase
{
	static $config = array(
         'application-id' => '57b5b7bde4b007f5ba82952b',
	     'application-secret' => '6k6sitD5hU'
    );
    static $configInvalid =  array(
         	'application-id' => '57b5b7bde4b007f5ba82952b',
            'application-secret' => '6k6sitD5hU'
    );
    static $configEmpty = array();
    static $aGroup = array('Unit1','Unit2','Unit3');
    static $mergeName = 'UnitTestMerge';
    static $difference = 'UnitDifference';
	public function testcreateContacts()
	{
		$contact = new api\Contacts( self::$config );
		$data = array(
	           'firstName' => 'Kesava',
	           'lastName' => 'Moorthi',
	           'mobile' => '+919025060261',
	           'country' => 'IN',
	           'vip' => 'false'
			);
		$response = json_decode( $contact->createContacts( $data ) );
		$this->assertObjectHasAttribute('id', $response );
		$throw['id'] = $response->id;
		$throw['mobile'] = $response->mobile;
		return $throw;
	}
	public function testcreateContactsFailure()
	{
		$contact = new api\Contacts( self::$config );
		$FalseData = array(
	           'firstName' => 'Kesava',
	           'lastName' => 'Moorthi',
	           // 'mobile' => '+919025060261',
	           'country' => 'IN',
	           'vip' => 'false'
			);
		$response = json_decode( $contact->createContacts( $FalseData ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testcreateContactsEmpty()
	{
		$contact = new api\Contacts( self::$config );
		$FalseData = array(
			);
		$response = json_decode( $contact->createContacts( $FalseData ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testretrieveAllContacts()
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveAllContacts() );
		$this->assertObjectHasAttribute('content', $response );
		return $response;
	}
	/**
	  * @depends testcreateContacts
	  */
	public function testretrieveSingleContacts( $response )
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveSingleContacts( $response['id'] ) );
		$this->assertObjectHasAttribute('id', $response );
		$this->assertObjectHasAttribute('firstName', $response );
		$this->assertObjectHasAttribute('mobile', $response );
	}
	public function testretrieveSingleContactsFailure()
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveSingleContacts( '7678bvjhbxh-khbvhjb' ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testretrieveSingleContactsEmpty()
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveSingleContacts( null ) );
		$this->assertObjectHasAttribute('content', $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testcheckIfContactExists( $response )
	{
		$contact = new api\Contacts( self::$config );		
		$response = $contact->checkExistContact( $response['mobile'] ) ;
		$this->assertTrue($response);				
		$this->assertEquals(1,$response);
		$this->assertNotEmpty( $response );
	}
	public function testcheckIfContactExistsFailure()
	{
		$contact = new api\Contacts( self::$config );
		$response = $contact->checkExistContact( '+0987654321' ) ;		
		$this->assertFalse($response);
		$this->assertEmpty( $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testaddContactToBlackLists( $responseObj )
	{
		$data =  array(
			array( 'id'     => $responseObj['id'] )
			);
		$service = 'Sms';
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->addContactToBlackLists( $data,$service ) );
		$this->assertObjectHasAttribute('updated', $response );
	}
	public function testaddContactToBlackListsFailure()
	{
		$data =  array(
			array( 'id'     => '875576465465465465' )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->addContactToBlackLists( $data ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testaddContactToBlackListsEmpty()
	{
		$data =  array(
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->addContactToBlackLists( $data ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testReturnContactsFromBlacklist()
	{
		$service = 'Sms';
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->getBlackListsContactService( $service ) );
		foreach( $response as $single ):
			$this->assertObjectHasAttribute('blacklistedServices', $single );
		endforeach;
	}
	public function testReturnContactsFromBlacklistFailure()
	{
		$service = 'dummy';
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->getBlackListsContactService( $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testReturnContactsFromBlacklistEmpty()
	{
		$service = null;
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->getBlackListsContactService( $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testRemoveGroupContactsBacklist( $responseObj )
	{
		$service = 'Sms';
		$data = array(
			'NotListed'
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeGroupOfContactFromBlackList( $data, $service ) );
		$this->assertObjectHasAttribute('updated', $response );
	}
	public function testRemoveGroupContactsBacklistFailure()
	{
		$service = 'DummY';
		$data = array(
			array( 'id' => '0876yf0898vjhvui6' )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeGroupOfContactFromBlackList( $data, $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testRemoveGroupContactsBacklistEmpty()
	{
		$service = null;
		$data = array(
			array( 'id' => null )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeGroupOfContactFromBlackList( $data, $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testRemoveContactsFrombacklist( $responseObj )
	{
		$this->testaddContactToBlackLists( $responseObj );
		$service = 'Sms';
		$data = array(
			array( 'id' => $responseObj['id'] )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeContactsFromBlacklists( $data, $service ) );
	}
	public function testRemoveContactsFrombacklistFailure()
	{
		$service = 'DummYDyu';
		$data = array(
			array( 'id' => 'ab07njsbvj78hjzvj' )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeContactsFromBlacklists( $data, $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testRemoveContactsFrombacklistEmpty()
	{
		$service = null;
		$data = array(
			array( 'id' => null )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->removeContactsFromBlacklists( $data, $service ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}

	/**
	 * @depends testcreateContacts
	 */
	public function testUpdateContact( $response )
	{
		$data = array(
			           'vip' => 'false',
			           'id'  => $response['id'],
			           'mobile'=> '+9178719624'.rand(00,99).'',
		               'groups' => array('All','NotListed'),
			         );
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->updateContact( $data, $response['id'] ) );
		if(isset($response->id))
		{
			$this->assertObjectHasAttribute('id', $response );
			$this->assertObjectHasAttribute('mobile', $response );
	    }
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testUpdateContactFailure( $response )
	{
		$data = array(
			           'vip' => 'false',
			           'id'  => $response['id'],
			           'mobile'=> '+9178719624'.rand(00,99).'',
		               'groups' => array('All','NotListed'),
			         );
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->updateContact( $data, $response['id'].'989' ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testUpdateContactEmpty( $response )
	{
		$data = array(
			         );
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->updateContact( null, null ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	
	
	public function testCreateLabels()
	{
		$data = array(
			array(
		       'name' => 'Company'.rand(000, 999),
		       'type' => 'Text'
		       )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->createLabel( $data ) );
		if(isset($response->code))
		{
          $this->assertObjectHasAttribute('developerMessage', $response );
		}
		else
		{
			foreach( $response as $singleResponse ):
				$this->assertObjectHasAttribute('name', $singleResponse );
			endforeach;
	    }
		return $response;
	}
	public function testCreateLabelsFailure()
	{
		$data = array(
			array(
		       'nameE' => 'Company',
		       'ttpe' => 'Text'
		       )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->createLabel( $data ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	public function testCreateLabelsEmpty()
	{
		$data = array(
			array(
		       'name' => null,
		       'type' => null
		       )
			);
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->createLabel( $data ) );
		$this->assertObjectHasAttribute('developerMessage', $response );
	}
	/**
	 * @depends testCreateLabels
	 */
	public function testRetrieveAccountContactLabels( $responseObj )
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveAccountContactsLabels() );
		$this->assertObjectHasAttribute( $responseObj[0]->name, $response );
	}
	public function testRetrieveAccountGroups()
	{
		$contact = new api\Contacts( self::$config );
		$response = json_decode( $contact->retrieveAccountGroups() );
		foreach( $response as $singleRes ):
			$this->assertObjectHasAttribute( 'name', $singleRes );
		endforeach;
	}
	public function testRetrieveOnAccountGroups()
	{
		$contact = new api\Contacts( self::$config );
		$arg = 'All';
		$response = json_decode( $contact->retrieveAccountGroupByName( $arg ) );
		$this->assertObjectHasAttribute( 'name', $response );
	}
	public function testRetrieveOnAccountGroupsFailure()
	{
		$contact = new api\Contacts( self::$config );
		$arg = 'yfyfyf';
		$response = json_decode( $contact->retrieveAccountGroupByName( $arg ) );
		$this->assertObjectHasAttribute( 'developerMessage', $response );
	}
	public function testRetrieveOnAccountGroupsEmpty()
	{
		$contact = new api\Contacts( self::$config );
		$arg = null;
		$response = json_decode( $contact->retrieveAccountGroupByName( $arg ) );
		foreach( $response as $singleRes ):
			$this->assertObjectHasAttribute( 'name', $singleRes );
		endforeach;
	}
	public function testRetrieveAccountGroupsPaged()
	{
		$contact = new api\Contacts( self::$config );
		$arg = array(
			'page' => '0',
			'size' => '3',
		);
		$response = json_decode( $contact->retrieveAccountGroupByPage( $arg ) );
		$this->assertObjectHasAttribute( 'content', $response );
	}
	public function testRetrieveAccountGroupsPagedFailure()
	{
		$contact = new api\Contacts( self::$config );
		$arg = array(
			'page' => 'NaN',
			'size' => 'NaN',
		);
		$response = json_decode( $contact->retrieveAccountGroupByPage( $arg ) );
		$this->assertObjectHasAttribute( 'content', $response );
	}
	public function testRetrieveAccountGroupsPagedEmpty()
	{
		$contact = new api\Contacts( self::$config );
		$arg = array(
			'page' => null,
			'size' => null,
		);
		$response = json_decode( $contact->retrieveAccountGroupByPage( $arg ) );
		$this->assertObjectHasAttribute( 'content', $response );
	}
	/**
	 * @depends testcreateContacts
	 */
	public function testDeleteSingleContact( $responseObj )
	{
		$contact = new api\Contacts( self::$config );
		$arg = $responseObj['id'];
		$response = json_decode( $contact->deleteContact( $arg ) );
		$this->assertObjectHasAttribute( 'id', $response );
	}
	public function testDeleteSingleContactFailure()
	{
		$contact = new api\Contacts( self::$config );
		$arg = 'iagfyg74567sf678';
		$response = json_decode( $contact->deleteContact( $arg ) );
		$this->assertObjectHasAttribute( 'developerMessage', $response );
	}
	public function testDeleteSingleContactEmpty()
	{
		$contact = new api\Contacts( self::$config );
		$arg = null;
		$response = json_decode( $contact->deleteContact( $arg ) );
		$this->assertObjectHasAttribute( 'developerMessage', $response );
	}
	
	public function testCreateGroupSuccess()
	{
        $aGroup = self::$aGroup;
        foreach($aGroup as $groupName)
        {
        	$data = array(
        	    'name' => $groupName
        	     );
        	$contact = new api\Contacts( self::$config );
		    $response = json_decode( $contact->createGroup( $data) );		    
		    $this->assertNotEmpty($response);
		    if(isset($response->name))
		    {
		    	$this->assertObjectHasAttribute('name', $response );
		        $this->assertObjectHasAttribute('size', $response );
		    }
		    else
		    {		    
				$this->markTestIncomplete(
				       'The '.$groupName.' name already exist.'
				);			    
		    }
        }
        return $aGroup;		
	}

	/**
     * @depends testCreateGroupSuccess
     */
	public function testCreateGroupFailure($aGroup)
	{
        foreach($aGroup as $groupName)
        {
        	$data = array(
        	    'name' => $groupName
        	     );
        	$contact = new api\Contacts( self::$config );
		    $response = json_decode( $contact->createGroup( $data) );		    
		    $this->assertNotEmpty($response);
		    $this->assertObjectHasAttribute('developerMessage', $response );
			$this->assertObjectHasAttribute('entity', $response );
			$this->assertEquals($groupName,$response->value);
        }       
	}

	/**
     * @depends testCreateGroupSuccess
     */
	public function testMergeMultipleGroupsSuccess($aGroup)
	{
		$data    = array( 
					'name' => self::$mergeName,
					'groups' => $aGroup
			       );
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->mergeMultipleGroups( $data) );
        $this->assertNotEmpty($response);       
		if(isset($response->name))
		{
			$this->assertObjectHasAttribute('name', $response );
		    $this->assertObjectHasAttribute('size', $response );
		}
		else
		{		    
			$this->markTestIncomplete(
			       'The '.self::$mergeName.' name already exist.'
			);			    
		}		
	}


	/**
     * @depends testCreateGroupSuccess
     */
	public function testMergeMultipleGroupsFailure($aGroup)
	{
		$data    = array( 
					'name' => self::$mergeName,
					'groups' => $aGroup
			       );
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->mergeMultipleGroups( $data) );   
		$this->assertNotEmpty($response);    
	    $this->assertObjectHasAttribute('developerMessage', $response );
	    $this->assertObjectHasAttribute('entity', $response );
	    $this->assertEquals($data['name'],$response->value);
		
	}

	/**
     * @depends testCreateGroupSuccess
     */
	public function testCreateGroupFromDifferenceSuccess($aGroup)
	{
		$data    = array( 
					'name' => self::$difference,
					'groups' => $aGroup
			       );
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->createGroupFromDifference( $data) );
        $this->assertNotEmpty($response);       
		if(isset($response->name))
		{
			$this->assertObjectHasAttribute('name', $response );
		    $this->assertObjectHasAttribute('size', $response );
		}
		else
		{		    
		   $this->markTestIncomplete(
			       'The '.self::$difference.' name already exist.'
			);
		}
	}

	/**
     * @depends testCreateGroupSuccess
     */
	public function testCreateGroupFromDifferenceFailure($aGroup)
	{
		$data    = array( 
					'name' => self::$difference,
					'groups' => $aGroup
			       );
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->createGroupFromDifference( $data) );        
		$this->assertNotEmpty($response);   
	    $this->assertObjectHasAttribute('developerMessage', $response );
	    $this->assertObjectHasAttribute('entity', $response );
	    $this->assertEquals($data['name'],$response->value);
		
	}
    
    /**
     * @depends testcreateContacts
     * @depends testCreateGroupSuccess     
     */

    public function testAddContactsToGroupByNameSuccess($response,$aGroup)
	{
		$contactid = $response['id'];
		$groupName = $aGroup[0];
        $data = array($contactid);
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->addContactsToGroupByName( $data,$groupName) );
        $this->assertNotEmpty($response);        
        if(isset($response->name))
		{
			$this->assertObjectHasAttribute('name', $response );
		    $this->assertObjectHasAttribute('size', $response );
		}
		
		return $groupName;
	}

	public function testAddContactsToGroupByNameFailure()
	{
		$contactid = '57c3bc190cf2d47a564a2a7dagasgsagsag';
		$groupName = self::$aGroup[0];
        $data = array($contactid);
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->addContactsToGroupByName( $data,$groupName) );
        $this->assertNotEmpty($response);       
        $this->assertObjectHasAttribute('developerMessage', $response );
	}

	/**
     * @depends testAddContactsToGroupByName
     */
	public function testViewContactsByGroupNameSuccess($groupName)
	{
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->viewContactsByGroupName($groupName) );
        $this->assertNotEmpty($response);          
        if(isset($response->content))
        {
        	foreach($response->content as $content)
        	{
        		$this->assertObjectHasAttribute('id', $content );
		        $this->assertObjectHasAttribute('mobile', $content );
		        $this->assertObjectHasAttribute('groups', $content );		       
        	}

        	 $this->assertObjectHasAttribute('size', $response );
		     $this->assertObjectHasAttribute('totalPages', $response );
        }
	}

	public function testViewContactsByGroupNameFailure()
	{
        $groupName = 'UnitTesting';
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->viewContactsByGroupName($groupName) );
        $this->assertNotEmpty($response);           
        $this->assertObjectHasAttribute('developerMessage', $response );
	}

	/**
     * @depends testcreateContacts
     * @depends testCreateGroupSuccess 
     */
	public function testDeleteContactsByGroupNameSuccess($response,$aGroup)
	{
        $contactid = $response['id'];
		$groupName = $aGroup[0];
		$data = array($contactid);
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->deleteContactsByGroupName($data,$groupName) );
        $this->assertNotEmpty($response);        
        if(isset($response->name))
		{
			$this->assertObjectHasAttribute('name', $response );
		    $this->assertObjectHasAttribute('size', $response );
		}
	}

	public function testDeleteContactsByGroupNameFailure()
	{
        $contactid = '57c3bc190cf2d47a564a2a7dagasgsagsag';
		$groupName = self::$aGroup[0];
		$data = array($contactid);
        $contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->deleteContactsByGroupName($data,$groupName) );
        $this->assertNotEmpty($response);
        $this->assertObjectHasAttribute('developerMessage', $response );        
        
	}

	/**
     * @depends testCreateGroupSuccess 
     */
	public function testDeleteGroupSuccess($aGroup)
	{
        array_push($aGroup,self::$mergeName);
        array_push($aGroup,self::$difference);    	
    	$contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->DeleteGroup($aGroup) );
        $this->assertNotEmpty($response);
        if(count($response) > 0)
		{
			foreach($response as $res)
			{
				$this->assertObjectHasAttribute('name', $res );
		        $this->assertObjectHasAttribute('deletedContacts', $res );
			}
		}
        
	}

	public function testDeleteGroupFailure()
	{
        $aGroup = array('UnitTesting');	
    	$contact  = new api\Contacts( self::$config );
        $response = json_decode( $contact->DeleteGroup($aGroup) );
        $this->assertNotEmpty($response);
        $this->assertObjectHasAttribute('developerMessage', $response );
        
	}
	public function testDeleteMultipleContacts()
	{
		$arg =array();
		$contact = new api\Contacts( self::$config );
		$responseObj = $this->testretrieveAllContacts();
		foreach( $responseObj->content as $singleContact ):
			$arg[]['id'] = $singleContact->id;
		endforeach;
		if(count($arg) > 0)
		{
			$response = json_decode( $contact->deleteMultipleContacts( $arg ) );
			$this->assertNotEmpty($response);
	    }
	}


}
?>