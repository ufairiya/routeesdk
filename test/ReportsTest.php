<?php
use PHPUnit\Framework\TestCase;
use Routee\lib\Api as api;

class ReportsTest extends TestCase
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
    public function testViewMsgRangeReport()
    {
		$data = array(
			'startDate' => '2015-01-01T00:00:00.000Z',
			'endDate' => '2017-01-01T00:00:00.000Z'
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewMsgRangeReport( $data ) );
    	$this->assertObjectHasAttribute('startDateTime', $result[0] );
    }
    public function testViewMsgRangeReportFailure()
    {
		$falseData = array(
			'startDate' => '2018-01-01T00:00:00.000Z',
			'endDate' => '2014-01-01T00:00:00.000Z'
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewMsgRangeReport( $falseData ) );
    	$this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testViewMsgRangeReportEmpty()
    {
		$emptyData = array(
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewMsgRangeReport( $emptyData ) );
    	$this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testViewCountryAnalytics()
    {
		$data = array(
			'startDate' => '2015-01-01T00:00:00.000Z',
			'endDate' => '2017-01-01T00:00:00.000Z',
			'mcc' => '404'
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewCountryAnalytics( $data ) );
    	for( $i=0; $i<count( $result ); $i++ )
    	{
	    	$this->assertObjectHasAttribute('mcc', $result[$i] );
    	}
    }
    public function testViewCountryAnalyticsFailure()
    {
		$data = array(
			'startDate' => '2017-01-01T00:00:00.000Z',
			'endDate' => '2014-01-01T00:00:00.000Z',
			'mcc' => '404'
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewCountryAnalytics( $data ) );
    	$this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testViewCountryAnalyticsEmpty()
    {
		$data = array(
			);
    	$report = new api\Reports( ReportsTest::$config );
    	$result = json_decode( $report->viewCountryAnalytics( $data ) );
    	$this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testViewVolPriceCntryNtwrk()
    {
        $data = array(
            'startDate' => '2015-01-01T00:00:00.000Z',
            'endDate' => '2017-01-01T00:00:00.000Z',
            'mcc' => '404',
            'mnc' => '43'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewVolPriceCntryNtwrk( $data ) );
        for( $i=0; $i<count( $result ); $i++ )
        {
            $this->assertObjectHasAttribute('mcc', $result[$i] );
        }
    }
    public function testViewVolPriceCntryNtwrkFailure()
    {
        $FalseData = array(
            'startDate' => '2017-01-01T00:00:00.000Z',
            'endDate' => '2014-01-01T00:00:00.000Z',
            'mcc' => '404',
            'mnc' => '43'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewVolPriceCntryNtwrk( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testViewVolPriceCntryNtwrkEmpty()
    {
        $FalseData = array(
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewVolPriceCntryNtwrk( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewCampaignAnalytics()
    {
        $data = array(
            'offset' => '+02:00', /* A time-zone offset from Greenwich/UTC, such as +02:00. */
            'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5' /* The id of the campaign that the messages belong to. */
            ); 
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignAnalytics( $data ) );
        for( $i=0; $i<count( $result ); $i++ )
        {
            $this->assertObjectHasAttribute('mcc', $result[$i] );
        }
    }
    public function testviewCampaignAnalyticsFailure()
    {
        $FalseData = array(
            'offset' => '+02:00', /* A time-zone offset from Greenwich/UTC, such as +02:00. */
            'campaignId' => "f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5\n" /* The id of the campaign that the messages belong to. */
            ); 
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewCampaignAnalyticsEmpty()
    {
        $FalseData = array(
            ); 
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewMsgRangeAnalytics()
    {
        $data = array(
            'startDate' => '2015-01-01T00:00:00.000Z',
            'endDate' => '2017-01-01T00:00:00.000Z'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewMsgRangeAnalytics( $data ) );
        $this->assertObjectHasAttribute('smsLatencyCount', $result );
    }
    public function testviewMsgRangeAnalyticsFailure()
    {
        $FalseData = array(
            'startDate' => '2018-01-01T00:00:00.000Z',
            'endDate' => '2014-01-01T00:00:00.000Z'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewMsgRangeAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewMsgRangeAnalyticsEmpty()
    {
        $FalseData = array(
            ); 
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewMsgRangeAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewTimeCountryAnalytics()
    {
        $data = array(
            'startDate' => '2015-01-01T00:00:00.000Z',
            'endDate' => '2017-01-01T00:00:00.000Z',
            'countryCode' => 'IN'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCountryAnalytics( $data ) );
        $this->assertObjectHasAttribute('smsLatencyCount', $result );
    }
    public function testviewTimeCountryAnalyticsFailure()
    {
        $FalseData = array(
            'startDate' => '2015-01-01T00:00:00.000Z',
            'endDate' => '2017-01-01T00:00:00.000Z',
            'countryCode' => 'CBEE'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCountryAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewTimeCountryAnalyticsEmpty()
    {
        $FalseData = array(
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCountryAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewTimeCntryNtwrkAnalytics()
    {
        $data = array(
            'startDate' => '2015-01-01T00:00:00.000Z',
            'endDate' => '2017-01-01T00:00:00.000Z',
            'mcc' => '404',
            'mnc' => '43'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCntryNtwrkAnalytics( $data ) );
        $this->assertObjectHasAttribute('smsLatencyCount', $result );
    }
    public function testviewTimeCntryNtwrkAnalyticsFailure()
    {
        $FalseData = array(
            'startDate' => '2018-01-01T00:00:00.000Z',
            'endDate' => '2014-01-01T00:00:00.000Z',
            'mcc' => '404',
            'mnc' => '43'
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCntryNtwrkAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewTimeCntryNtwrkAnalyticsEmpty()
    {
        $FalseData = array(
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewTimeCntryNtwrkAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewCampaignTimeAnalytics()
    {
        $data = array(
            'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5' /* The id of the campaign that the messages belong to. */
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignTimeAnalytics( $data ) );
        $this->assertObjectHasAttribute('smsLatencyCount', $result );
    }
    public function testviewCampaignTimeAnalyticsFailure()
    {
        $FalseData = array(
            'campaignId' => 'f7691dc9-2ccc-4f5b-af29-aa61acb9cbd5 888' /* The id of the campaign that the messages belong to. */
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignTimeAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
    public function testviewCampaignTimeAnalyticsEmpty()
    {
        $FalseData = array(
            );
        $report = new api\Reports( ReportsTest::$config );
        $result = json_decode( $report->viewCampaignTimeAnalytics( $FalseData ) );
        $this->assertObjectHasAttribute('developerMessage', $result );
    }
}
?>