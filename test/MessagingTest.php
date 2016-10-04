<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as api;

class MessagingTest extends TestCase
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
    static $callbackurl = 'http://stallioni.in/470-routee/callback.php';
    public function testSendSingleSMSSuccess()
    {
        $messaging = api\Messaging::getInstance( self::$config );
           $data_sms = array(
            'body' =>'Test Message- stallioni Routee SDK',
            'to'   => '919787136232',
            'from' => '919600951898',
            'flash'=> false,
            'label'=>'stallioni Routee',
            'callback' => array(
                'strategy' => 'OnChange',
                'url'      => self::$callbackurl,
                )
            );
        
        $response = $messaging->sendSingleSMS($data_sms);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('trackingId', $result);
        $this->assertObjectHasAttribute('status', $result);
        $this->assertObjectHasAttribute('to', $result);
        $this->assertObjectHasAttribute('body', $result);
        $this->assertEquals($data_sms['body'],$result->body);
        $this->assertEquals($data_sms['from'],$result->from);
        $this->assertEquals($data_sms['label'],$result->label);
        return $messageid = (isset($result->trackingId) && $result->trackingId != '') ? $result->trackingId : '';
    }

    public function testSendSingleSMSFailure()
    {
        $messaging = api\Messaging::getInstance( self::$config );
           $data_sms = array(
            'body' =>'Unit Test Message- stallioni Routee SDK',
            'to'   => '91978713623289898989',
            'from' => '919600951898',
            'flash'=> false,
            'label'=>'stallioni Routee',
            'callback' => array(
                'strategy' => 'OnChange',
                'url'      => self::$callbackurl,
                )
            );
        $response = $messaging->sendSingleSMS($data_sms);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertEquals('Validation Error!',$result->developerMessage);
        $this->assertEquals('Invalid mobile number',$result->properties->to);        
    }
    public function testGetAnalyzeSingleMessageSuccess()
    {
        $messaging = api\Messaging::getInstance( self::$config );
           $data_sms = array(
            'body' =>'Test Message- stallioni Routee SDK',
            'to'   => '919787136232',
            'from' => '919600951898',
            'flash'=> false,
            'label'=>'stallioni Routee',
            'callback' => array(
                'strategy' => 'OnChange',
                'url'      => self::$callbackurl,
                )
            );
        $response = $messaging->getAnalyzeSingleMessage($data_sms);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('bodyAnalysis', $result);
        $this->assertObjectHasAttribute('cost', $result);
        $this->assertArrayHasKey('parts',(array)$result->bodyAnalysis);
        $this->assertArrayHasKey('characters',(array)$result->bodyAnalysis);
        $this->assertArrayHasKey('unicode',(array)$result->bodyAnalysis);        
    }
    public function testGetAnalyzeSingleMessageFailure()
    {
        $messaging = api\Messaging::getInstance( self::$config );
           $data_sms = array(
            'body' =>'Unit Test Message- stallioni Routee SDKs123',
            'to'   => '91978713623289898989',
            'from' => '919600951898',
            'flash'=> false,
            'label'=>'stallioni Routee',
            'callback' => array(
                'strategy' => 'OnChange',
                'url'      => self::$callbackurl,
                )
            );
        $response = $messaging->getAnalyzeSingleMessage($data_sms);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertEquals('Validation Error!',$result->developerMessage);                     
    }

    public function testSendCampaignSuccess()
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'to' => array('+919787136232'),
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->sendCampaign($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('trackingId', $result);
        $this->assertObjectHasAttribute('state', $result);
        $this->assertObjectHasAttribute('to', $result);
        $this->assertObjectHasAttribute('body', $result);
        $this->assertEquals($data_camp['body'],$result->body);
        $this->assertEquals($data_camp['from'],$result->from);
        $this->assertObjectHasAttribute('reminder', $result);
        $this->assertObjectHasAttribute('fallbackValues', $result);
        $this->assertEquals($data_camp['fallbackValues']['firstName'],$result->fallbackValues->firstName);
         return $camp_messageid = $result->trackingId;     
    }
    public function testSendCampaignFailure()
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'campaignName' => 'Unit Test SDK-'.time(), 
            
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->sendCampaign($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('developerMessage', $result);
        $this->assertEquals(004,$result->errors[0]->errorCode);
        $this->assertEquals('No recipients are set for the sms',$result->errors[0]->developerMessage);             
    }

    public function testAnalyzeCampaignSMSSuccess()
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'to' => array('+919787136232'),
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->analyzeCampaignSMS($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertGreaterThanOrEqual(1,$result->numberOfRecipients);
        $this->assertObjectHasAttribute('recipientsPerCountry', $result);
        $this->assertObjectHasAttribute('bodyAnalysis', $result);
        $this->assertArrayHasKey('parts',(array)$result->bodyAnalysis);
        $this->assertArrayHasKey('characters',(array)$result->bodyAnalysis);
        $this->assertArrayHasKey('unicode',(array)$result->bodyAnalysis);
    }
    public function testAnalyzeCampaignSMSFailure()
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '9196009518988989',       
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->analyzeCampaignSMS($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertEquals('SMS contains invalid data',$result->developerMessage);
        $this->assertEquals(004,$result->errors[0]->errorCode);
        $this->assertEquals('No recipients are set for the sms',$result->errors[0]->developerMessage); 
        $this->assertTrue(true);
        return $argument = 'a072e10f-d683-43c9-b631-63ad8e4f4d74';       

    }

    /**
     * @depends testSendSingleSMSSuccess
     */
    public function testTrackSingleSMSbyIdSuccess($messageid)
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $messageid = ($messageid != '') ? $messageid : 'a072e10f-d683-43c9-b631-63ad8e4f4d74';
        $response = $messaging->trackSingleSMSbyId($messageid);
        $result = json_decode($response); 
        foreach($result as $for_res)
        {
            $this->assertEquals($messageid,$for_res->messageId);
            $this->assertObjectHasAttribute('operator', $for_res);
            $this->assertObjectHasAttribute('from', $for_res);
            $this->assertObjectHasAttribute('price', $for_res);
            $this->assertObjectHasAttribute('to', $for_res);
        }    
       
    }

    /**
     * @depends testSendSingleSMSSuccess
     */
    public function testTrackSingleSMSbyIdFailure($messageid)
    {
        $messaging = api\Messaging::getInstance( self::$configInvalid );
        $messageid = ($messageid != '') ? $messageid : 'a072e10f-d683-43c9-b631-63ad8e4f4d74';
        $response = $messaging->trackSingleSMSbyId($messageid);
        $result = json_decode($response); 
        $this->assertNotEmpty($result);
        $this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result);       
    }

    /**
     * @depends testSendCampaignSuccess
     */
    public function testTrackCampaignMultiSMSSuccess($camp_messageid)
    {
        $messaging = api\Messaging::getInstance( self::$config );
        $camp_messageid = ($camp_messageid != '') ? $camp_messageid : '125e05df-0dc9-41de-b3f7-4e0f71fe4f04';
        $param = array('page'=>'0' );
        $response = $messaging->trackCampaignMultiSMS($camp_messageid,$param);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        foreach($result->content as $for_res)
        {
            $this->assertObjectHasAttribute('operator', $for_res);
            $this->assertObjectHasAttribute('from', $for_res);
            $this->assertObjectHasAttribute('price', $for_res);
            $this->assertObjectHasAttribute('to', $for_res);
            $this->assertObjectHasAttribute('campaignName', $for_res);
            
        } 
    }

    /**
     * @depends testSendCampaignSuccess
     */
    public function testTrackCampaignMultiSMSFailure($camp_messageid)
    {
        $messaging = api\Messaging::getInstance( self::$configInvalid );
        $camp_messageid = ($camp_messageid != '') ? $camp_messageid : '125e05df-0dc9-41de-b3f7-4e0f71fe4f04';
        $response = $messaging->trackCampaignMultiSMS($camp_messageid);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertEquals(401,$result->status);        
        $this->assertObjectHasAttribute('message', $result); 
    }
    
    public function testFilterMultipleSMSSuccess()
    { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data = array(
                    'filter_param' => array(
                        array(
                            'fieldName'  => 'smsId',
                            'searchTerm' => '335c5ec5-bc82-415d-af94-ad884da23d56'
                            )
                        )
                    );
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        foreach($result->content as $for_res)
        {
            $this->assertObjectHasAttribute('operator', $for_res);
            $this->assertObjectHasAttribute('from', $for_res);
            $this->assertObjectHasAttribute('price', $for_res);
            $this->assertObjectHasAttribute('to', $for_res);
            $this->assertObjectHasAttribute('country', $for_res);
            
        } 
   }

   public function testFilterMultipleSMSwithQureyParamSuccess()
    { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data = array(
                    'filter_param' => array(
                        array(
                            'fieldName'  => 'smsId',
                            'searchTerm' => '335c5ec5-bc82-415d-af94-ad884da23d56'
                            )
                        ),
                    'query_param' => array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z')
        );
        
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        foreach($result->content as $for_res)
        {
            $this->assertObjectHasAttribute('operator', $for_res);
            $this->assertObjectHasAttribute('from', $for_res);
            $this->assertObjectHasAttribute('price', $for_res);
            $this->assertObjectHasAttribute('to', $for_res);
            $this->assertObjectHasAttribute('country', $for_res);
            
        }        
   }
   public function testFilterMultipleSMSwithQureyParamDataPostEmptySuccess()
    { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data = array(                    
                    'query_param' => array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z')
        );
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        foreach($result->content as $for_res)
        {
            $this->assertObjectHasAttribute('operator', $for_res);
            $this->assertObjectHasAttribute('from', $for_res);
            $this->assertObjectHasAttribute('price', $for_res);
            $this->assertObjectHasAttribute('to', $for_res);
            $this->assertObjectHasAttribute('country', $for_res);
            
        }         
   }

    public function testFilterMultipleSMSwithParamEmptySuccess()
    { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data = array();
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        if(isset($result->content))
        {
            foreach($result->content as $for_res)
            {
                $this->assertObjectHasAttribute('operator', $for_res);
                $this->assertObjectHasAttribute('from', $for_res);
                $this->assertObjectHasAttribute('price', $for_res);
                $this->assertObjectHasAttribute('to', $for_res);
                $this->assertObjectHasAttribute('country', $for_res);
                
            }
       }
       else
       {
            $this->assertEquals(401,$result->status);        
            $this->assertObjectHasAttribute('message', $result);
       }                
   }
   public function testFilterMultipleSMSwithParamEmptyDataSuccess()
    { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data = array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z');
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        if(isset($result->content))
        {
            foreach($result->content as $for_res)
            {
                $this->assertObjectHasAttribute('operator', $for_res);
                $this->assertObjectHasAttribute('from', $for_res);
                $this->assertObjectHasAttribute('price', $for_res);
                $this->assertObjectHasAttribute('to', $for_res);
                $this->assertObjectHasAttribute('country', $for_res);
                
            }
        }
        else
       {
            $this->assertEquals(401,$result->status);        
            $this->assertObjectHasAttribute('message', $result);
       }                
   }

   public function testFilterMultipleSMSwithParamEmptyDataFailure()
    { 
        $messaging = api\Messaging::getInstance( self::$configInvalid );
        $data = array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z');
        $response =$messaging->filterMultipleSMS($data);
        $result = json_decode($response);
        if(isset($result->content))
        {
            foreach($result->content as $for_res)
            {
                $this->assertObjectHasAttribute('operator', $for_res);
                $this->assertObjectHasAttribute('from', $for_res);
                $this->assertObjectHasAttribute('price', $for_res);
                $this->assertObjectHasAttribute('to', $for_res);
                $this->assertObjectHasAttribute('country', $for_res);
                
            }
        }
        else
       {
            $this->assertEquals(401,$result->status);        
            $this->assertObjectHasAttribute('message', $result);
       }                
   }

   public function testRetriveCountriesQuietHourSuccess()
   { 
        $messaging = api\Messaging::getInstance( self::$config );
        $countryID = 'en';
        $response =$messaging->retriveCountriesQuietHour($countryID);
        $result = json_decode($response);
        if(count((array) $result > 0 ))
        {
            foreach($result as $res)
            {
                $this->assertObjectHasAttribute('code', $res);
                $this->assertObjectHasAttribute('name', $res);
                $this->assertObjectHasAttribute('localeName', $res);
                $this->assertObjectHasAttribute('supported', $res);
            }
        }
                    
   }

   public function testRetriveCountriesQuietHourFailure()
   { 
        $messaging = api\Messaging::getInstance( self::$config );
        $countryID = '';
        $response =$messaging->retriveCountriesQuietHour($countryID);
        $result = json_decode($response);
        $this->assertEquals(404,$result->status);        
        $this->assertObjectHasAttribute('message', $result);
        $this->assertObjectHasAttribute('error', $result);                    
   }

   public function testCreateScheduledCampaignSuccess()
   { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'scheduledDate'=>strtotime('+30 days'),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'to' => array('+919787136232'),
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->sendCampaign($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('trackingId', $result);
        $this->assertObjectHasAttribute('state', $result);
        $this->assertObjectHasAttribute('to', $result);
        $this->assertObjectHasAttribute('body', $result);
        $this->assertEquals($data_camp['body'],$result->body);
        $this->assertEquals($data_camp['from'],$result->from);
        $this->assertObjectHasAttribute('reminder', $result);
        $this->assertObjectHasAttribute('fallbackValues', $result);
        $this->assertEquals($data_camp['fallbackValues']['firstName'],$result->fallbackValues->firstName);
        return $schedulr_camp_messageid =$result->trackingId;                    
   }

   /**
     * @depends testCreateScheduledCampaignSuccess
     */

   public function testUpdateScheduledCampaignSuccess($schedulr_camp_messageid)
   { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Updated Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'scheduledDate'=>strtotime('+30 days'),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'to' => array('+919787136232'),
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->updateScheduledCampaign($data_camp,$schedulr_camp_messageid);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('trackingId', $result);
        $this->assertObjectHasAttribute('state', $result);
        $this->assertObjectHasAttribute('to', $result);
        $this->assertObjectHasAttribute('body', $result);
        $this->assertEquals($data_camp['body'],$result->body);
        $this->assertEquals($data_camp['from'],$result->from);
        $this->assertObjectHasAttribute('reminder', $result);
        $this->assertObjectHasAttribute('fallbackValues', $result);
        $this->assertEquals($data_camp['fallbackValues']['firstName'],$result->fallbackValues->firstName);                             
   }

   /**
    * @depends testCreateScheduledCampaignSuccess
    */

   public function testRetrieveDetailsCampaignSuccess($schedulr_camp_messageid)
   {
       $messaging = api\Messaging::getInstance( self::$config );
       $response = $messaging->retrieveDetailsCampaign($schedulr_camp_messageid);
       $result = json_decode($response);
       $this->assertObjectHasAttribute('trackingId', $result);
       $this->assertObjectHasAttribute('state', $result);
       $this->assertObjectHasAttribute('to', $result);
       $this->assertObjectHasAttribute('body', $result);
       $this->assertObjectHasAttribute('campaignName', $result);
       $this->assertObjectHasAttribute('smsAnalysis', $result);
       $this->assertObjectHasAttribute('reminder', $result);           
   }
   
   /**
    * @depends testCreateScheduledCampaignSuccess
    */

   public function testDeleteScheduledCampaignSuccess($schedulr_camp_messageid)
   {
       $messaging = api\Messaging::getInstance( self::$config );
       $response = $messaging->deleteScheduledCampaign($schedulr_camp_messageid);
       $result = json_decode($response);
       $this->assertEmpty($result);
   }


   public function testCreateScheduledCampaignFailure()
   { 
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898', 
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'scheduledDate'=>strtotime('+30 days'),
            'campaignName' => 'Unit Test SDK-'.time(),    
           
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->sendCampaign($data_camp);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('developerMessage', $result);
        $this->assertEquals(004,$result->errors[0]->errorCode);
        $this->assertEquals('No recipients are set for the sms',$result->errors[0]->developerMessage);
                      
   }

   public function testUpdateScheduledCampaignFailure()
   { 
        $schedulr_camp_messageid = '97c82c4f-8c78-426e-93c9-f354e4c603b38';
        $messaging = api\Messaging::getInstance( self::$config );
        $data_camp = array(
            'body'=>'Hi [~firstName], Updated Unit Test Message- stallioni Routee SDK',        
            'from'=> '919600951898',       
            'reminder' => array(
                'minutesAfter'=> '5',
                'minutesBefore'=> '5',
                'to' => array('+919787136232'),
                ),
            'callback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'flash' => false,
            'smsCallback' => array(
                  "strategy" => "OnChange",
                  "url"=>self::$callbackurl,
                ),
            'scheduledDate'=>strtotime('+30 days'),
            'campaignName' => 'Unit Test SDK-'.time(),       
            'to' => array('+919787136232'),
            'fallbackValues' => array('firstName'=>'Gokul'),
        );
        $response = $messaging->updateScheduledCampaign($data_camp,$schedulr_camp_messageid);
        $result = json_decode($response);
        $this->assertNotEmpty($result);
        $this->assertObjectHasAttribute('developerMessage', $result);
        $this->assertObjectHasAttribute('value', $result);
        $this->assertObjectHasAttribute('entity', $result);       
                                    
   }

   public function testDeleteScheduledCampaignFailure()
   {
       $schedulr_camp_messageid = '';
       $messaging = api\Messaging::getInstance( self::$config );
       $response = $messaging->deleteScheduledCampaign($schedulr_camp_messageid);
       $result = json_decode($response);       
       $this->assertNotEmpty($result);
       $this->assertObjectHasAttribute('developerMessage', $result);
       $this->assertEquals(500,$result->code);       
   }

    public function testRetrieveDetailsCampaignFailure()
   {
       $schedulr_camp_messageid = 'a34f6686-e791-431d-856c-668bdf1c8a2er';
       $messaging = api\Messaging::getInstance( self::$config );
       $response = $messaging->retrieveDetailsCampaign($schedulr_camp_messageid);
       $result = json_decode($response);
       $this->assertNotEmpty($result);
       $this->assertObjectHasAttribute('developerMessage', $result);
       $this->assertObjectHasAttribute('value', $result);
       $this->assertObjectHasAttribute('entity', $result);
   }
    
}
?>