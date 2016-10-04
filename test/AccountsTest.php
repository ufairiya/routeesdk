<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as api;

class AccountsTest extends TestCase
{
	static $config = array(
         'application-id' => '57b5b7bde4b007f5ba82952b',
         'application-secret' => '6k6sitD5hU'
    );
    static $configInvalid =  array(
         	'application-id' => '57b5b7bde4b007f5ba82952b',
         	'application-secret' => '6k6sitD5hU123'
        );
    static $configEmpty = array();
	public function testAccountBalanceSuccess()
	{
		$account = api\Accounts::getInstance( self::$config );
		$response = $account->retrieveAccountBal();
		$result = json_decode($response);
		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('sign',(array)$result->currency);
        $this->assertObjectHasAttribute('balance', $result);
	}
	public function testAccountBalanceFailureEmptyConfig()
	{		
		$account = api\Accounts::getInstance(self::$configEmpty );
		$response = $account->retrieveAccountBal();
		$result = json_decode($response);
		if(isset($result->status)){      
        	$this->assertEquals(401,$result->status);        
        	$this->assertObjectHasAttribute('message', $result);
        }		
				
	}
	public function testAccountBalanceFailureInvalidConfig()
	{		
		$account = api\Accounts::getInstance(self::$configInvalid);
		$response = $account->retrieveAccountBal();
		$result = json_decode($response);        
        $this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result);		
				
	}
	public function testRetrieveRouteeServicesSuccess()
	{
		$account = api\Accounts::getInstance( self::$config );
		$response = $account->retrieveRouteeServices();
		$result = json_decode($response);
		$this->assertNotEmpty($result);		
		if(isset($result->currency))
		{
			$this->assertArrayHasKey('sign',(array)$result->currency);
	    }
        $this->assertObjectHasAttribute('sms', $result);
        $this->assertObjectHasAttribute('twoStep', $result);
	}
	public function testRetrieveRouteeServicesFailureEmptyConfig()
	{
		$account = api\Accounts::getInstance( self::$configEmpty );
		$response = $account->retrieveRouteeServices();
		$result = json_decode($response);
		if(isset($result->status)){		
		    $this->assertEquals(401,$result->status);      
            $this->assertObjectHasAttribute('message', $result);
        }
	}
	public function testRetrieveRouteeServicesFailureInvalidConfig()
	{
		$account = api\Accounts::getInstance( self::$configInvalid );
		$response = $account->retrieveRouteeServices();
		$result = json_decode($response);		
		$this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result);
	}

	public function testRetrieveAccountTransactionsSuccess()
	{
		$account = api\Accounts::getInstance( self::$config );
		$response = $account->retrieveAccountTransactions();
		$result = json_decode($response);
		$this->assertNotEmpty($result);
		$this->assertObjectHasAttribute('content', $result);
		$this->assertObjectHasAttribute('size', $result);
	}
	public function testRetrieveAccountTransactionsFailureEmptyConfig()
	{
		$account = api\Accounts::getInstance( self::$configEmpty );
		$response = $account->retrieveAccountTransactions();
		$result = json_decode($response);		
		if(isset($result->status)){		
		    $this->assertEquals(401,$result->status);      
            $this->assertObjectHasAttribute('message', $result);
        }
	}
	public function testRetrieveAccountTransactionsFailureInvalidConfig()
	{
		$account = api\Accounts::getInstance( self::$configInvalid );
		$response = $account->retrieveAccountTransactions();
		$result = json_decode($response);		
		$this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result);
	}

	public function testRetrieveAvailBankAccountsSuccess()
	{
		$account = api\Accounts::getInstance( self::$config );
		$response = $account->retrieveAvailBankAccounts();
		$result = json_decode($response);
		$this->assertNotEmpty($result);
		$this->assertObjectHasAttribute('name', $result);
		$this->assertObjectHasAttribute('address', $result);
		$this->assertObjectHasAttribute('phone', $result);
		$this->assertObjectHasAttribute('banks', $result);
	}
	public function testRetrieveAvailBankAccountsFailureEmptyConfig()
	{
		$account = api\Accounts::getInstance( self::$configEmpty );
		$response = $account->retrieveAvailBankAccounts();
		$result = json_decode($response);		
		if(isset($result->status)){		
		    $this->assertEquals(401,$result->status);      
            $this->assertObjectHasAttribute('message', $result);
        }
	}
	public function testRetrieveAvailBankAccountsFailureInvalidConfig()
	{
		$account = api\Accounts::getInstance( self::$configInvalid );
		$response = $account->retrieveAvailBankAccounts();
		$result = json_decode($response);		
		$this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result);
	}

}
?>