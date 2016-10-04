<?php
/* TRACK MULTIPLE SMS WITH FILTERS FOR A SPECIFIC TIME RANGE */
require_once  '../../vendor/autoload.php';
$config = array(
    'application-id' => '57b5b7bde4b007f5ba82952b',
    'application-secret' => '6k6sitD5hU'
    );
use Routee\lib\Api as api;
$sms = new api\Messaging($config);
 $data = array(
                    'filter_param' => array(
                        array(
                            'fieldName'  => 'smsId',
                            'searchTerm' => '335c5ec5-bc82-415d-af94-ad884da23d56'
                            )
                        ),
                    'query_param' => array('dateStart' => '2016-08-19T15:00Z','dateEnd' => '2016-08-27T15:00Z')
        );
$SMSfilterResponse = $sms->filterMultipleSMS( $data );
print_r( $SMSfilterResponse );
?>