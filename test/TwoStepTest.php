<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as api;

class TwoStepTest extends TestCase
{
	static $config = array(
         'application-id' => '57bd7450e4b07bf187df66ed',
	     'application-secret' => 'tC1XhTGae4'
    );
    static $configInvalid =  array(
         	'application-id' => '57b5b7bde4b007f5ba82952b',
            'application-secret' => '6k6sitD5hU'
        );
    static $configEmpty = array();
    public function testStart2StepVerificationSuccess()
	{
		$twostep = api\TwoStep::getInstance( self::$config );
		$data = array(
			'method' => 'sms',
			'type'   => 'code',
			'recipient'   => '+919787136232'
		);
		$response = $twostep->start2StepVerification($data);
		$result = json_decode($response);
		$this->assertNotEmpty($result);		
		$this->assertObjectHasAttribute('trackingId', $result);
		$this->assertObjectHasAttribute('status', $result);
		return $trackid_2step = $result->trackingId;	
	}
	public function testStart2StepVerificationFailure()
	{
		$twostep = api\TwoStep::getInstance( self::$configInvalid );
		$data = array(
			'method' => 'sms',
			'type'   => 'code',
			'recipient'   => '+919787136232'
		);
		$response = $twostep->start2StepVerification($data);
		$result = json_decode($response);
		$this->assertNotEmpty($result);
		$this->assertObjectHasAttribute('developerMessage', $result);		
	}

	/**
	 * @depends testStart2StepVerificationSuccess
	 */

	public function testRetrieve2StepStatusSuccess($trackid_2step)
	{
		$twostep = api\TwoStep::getInstance( self::$config );		
		$response = $twostep->retrieve2StepStatus($trackid_2step);
		$result = json_decode($response);
		$this->assertNotEmpty($result);	
		$this->assertObjectHasAttribute('trackingId', $result);
		$this->assertObjectHasAttribute('status', $result);		
	}

	public function testRetrieve2StepStatusFailure()
	{
		$twostep = api\TwoStep::getInstance( self::$config );
		$trackid = '';
		$response = $twostep->retrieve2StepStatus();
		$result = json_decode($response);
		$this->assertNotEmpty($result);
		$this->assertObjectHasAttribute('message', $result);
	}

	/**
	 * @depends testStart2StepVerificationSuccess
	 */

	public function testCancel2StepStatusSuccess($trackid_2step)
	{
		$twostep = api\TwoStep::getInstance( self::$config );		
		$response = $twostep->cancel2StepStatus($trackid_2step);
		$result = json_decode($response);
		$this->assertNotEmpty($result);	
		if(isset($result->trackingId))
		{
			$this->assertObjectHasAttribute('trackingId', $result);
			$this->assertObjectHasAttribute('status', $result);
		}
		else
		{
		    $this->assertObjectHasAttribute('developerMessage', $result);
		    $this->markTestSkipped(
              'The cancellation already done.'
            );	
		}	
	}

	public function testConfirm2StepStatusSuccess()
	{
		$twostep = api\TwoStep::getInstance( self::$config );
		$trackid = '90be6b78-7104-460c-81d1-bb0520aff477';
		$data = array('answer'=>'0984');
		$response = $twostep->confirm2StepStatus($data,$trackid);
		$result = json_decode($response);
		$this->assertNotEmpty($result);	
		if(isset($result->trackingId))
		{
			$this->assertObjectHasAttribute('trackingId', $result);
			$this->assertObjectHasAttribute('status', $result);
		}
		else
		{
		    $this->assertObjectHasAttribute('developerMessage', $result);
		    $this->markTestSkipped(
              'The confirmation already done.'
            );	
		}	
	}

	public function testRetrieve2StepAccountReportSuccess()
	{
		$twostep = api\TwoStep::getInstance( self::$config );
		$trackid = '';
		$response = $twostep->retrieve2StepAccountReport();
		$result = json_decode($response);
		$this->assertNotEmpty($result);		
		$this->assertObjectHasAttribute('total', $result);
		$this->assertObjectHasAttribute('perCountry', $result);
	}
	public function testRetrieve2StepAccountReportFailure()
	{
		$twostep = api\TwoStep::getInstance( self::$configInvalid );
		$trackid = '';
		$response = $twostep->retrieve2StepAccountReport();
		$result = json_decode($response);
		$this->assertNotEmpty($result);		       
        $this->assertObjectHasAttribute('developerMessage', $result);
	}
	public function testRretrieve2StepAppReportSuccess()
	{
		$twostep = api\TwoStep::getInstance( self::$config );
		$appid = self::$config['application-id'];
		$response = $twostep->retrieve2StepAppReport($appid );
		$result = json_decode($response);
		$this->assertNotEmpty($result);	
		$this->assertObjectHasAttribute('applicationId', $result);
		$this->assertObjectHasAttribute('total', $result);
	}
	public function testRretrieve2StepAppReportFailure()
	{
		$twostep = api\TwoStep::getInstance( self::$configInvalid );
		$appid = self::$config['application-id'];
		$response = $twostep->retrieve2StepAppReport($appid );
		$result = json_decode($response);
		$this->assertNotEmpty($result);	
		$this->assertObjectHasAttribute('developerMessage', $result);
	}
}
?>