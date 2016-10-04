<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as auth;

class Authorization extends TestCase
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
    public function testAppCredentials()
    {
        $authResponse = new auth\Authorization();
        $authResult = $authResponse->getAuthorization(self::$config);
        $response = json_decode($authResult);
        $this->assertContains('MT_ROLE_SMS',$response->permissions);
        $this->assertObjectHasAttribute('access_token', $response);
       
    }
    public function testAppFaliureCredentials()
    {
        $authResponse = new auth\Authorization();
        $authResult = $authResponse->getAuthorization(self::$configInvalid);
        $response = json_decode($authResult);
        $this->assertNotEmpty($authResult);       
        $this->assertEquals(401,$response->status);        
        $this->assertObjectHasAttribute('message', $response);
       
    }
     public function testAppFaliureEmptyCredentials()
    {
        $authResponse = new auth\Authorization();
        $authResult = $authResponse->getAuthorization(self::$configEmpty);
        $response = json_decode($authResult);
        if(isset($response->status)){      
            $this->assertEquals(401,$response->status); 
            $this->assertInternalType('string',$response->message);
            $this->assertObjectHasAttribute('message', $response);
        }
        
    }
}
?>