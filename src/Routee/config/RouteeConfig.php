<?php
/**
 *
 * The Routee configuration details can be declared .
 *
 * @package Routee\config
 * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */
    
namespace Routee\config;
    
use Routee\Exception;

/**
 * Class RouteeConfig
 * 
 *
 * @package Routee
 */


class RouteeConfig
{
   
    /**
     * Some default configuration for Routee
     *
     * @return array
     */ 
    
    const CONNECTURL = 'https://connect.routee.net';

    const AUTHURL = 'https://auth.routee.net';

    /**
     * Set Routee API call Urls for account,authentcate,contacts etc.
     *
     * @return array
     */ 
     
    public function getDefaultUrl()
    {
        $defaultUrl = array(
            'authUrl' => self::AUTHURL.'/oauth/token',
            'accountBalUrl' => self::CONNECTURL.'/accounts/me/balance',
            'routeeServiceUrl' => self::CONNECTURL.'/system/prices',
            'accountTransUrl' => self::CONNECTURL.'/accounts/me/transactions',
            'availBankAccountsUrl' => self::CONNECTURL.'/accounts/me/banks',
            'contactUrl' => self::CONNECTURL.'/contacts/my',
            'contactBlackListUrl' => self::CONNECTURL.'/contacts/my/blacklist',
            'contactLabelUrl' => self::CONNECTURL.'/contacts/labels/my',
            'contactGroupUrl' => self::CONNECTURL.'/groups/my',
            'contactGroupPageUrl' => self::CONNECTURL.'/groups/my/page',
            'contactGroupMergeUrl' => self::CONNECTURL.'/groups/my/merge',
            'contactGroupDifferenceeUrl' => self::CONNECTURL.'/groups/my/difference',
            'contactGroupNameUrl' => self::CONNECTURL.'/groups/my/{group_name}/contacts',
            'messagingSendSingleSMSUrl' => self::CONNECTURL.'/sms',
            'analyzeSingleMessageUrl' => self::CONNECTURL.'/sms/analyze',
            'messagingSendSMSCampaignUrl' => self::CONNECTURL.'/sms/campaign',
            'analyzeSMSCampaignMessageUrl' => self::CONNECTURL.'/sms/analyze/campaign',
            'trackingSingleSMSUrl' => self::CONNECTURL.'/sms/tracking/single',
            'trackingCampaignSMSUrl' => self::CONNECTURL.'/sms/tracking/campaign',
            'trackingSMSUrl' => self::CONNECTURL.'/sms/tracking',
            'countriesQuietHrsUrl' => self::CONNECTURL.'/sms/quietHours/countries',
            'SMSUrl'  => self::CONNECTURL.'/sms',
            'campaignsUrl' => self::CONNECTURL.'/campaigns',
            'volPriceUrl' => self::CONNECTURL.'/reports/my/volPrice',
            'volPricePerMessageUrl' => self::CONNECTURL.'/reports/my/volPrice/perMcc',
            'volPricePerMsgCountryNtwrkUrl' => self::CONNECTURL.'/reports/my/volPrice/perMccMnc',
            'volPricePerCampaignUrl' => self::CONNECTURL.'/reports/my/volPrice/perCampaign',
            'msgRangeAnalyticsUrl' => self::CONNECTURL.'/reports/my/latency',
            'latencyPerCountryUrl' => self::CONNECTURL.'/reports/my/latency/perCountry',
            'latencyPerCountryPerNtwrkUrl' => self::CONNECTURL.'/reports/my/latency/perMccMnc',
            'latencyPerCampaignUrl' => self::CONNECTURL.'/reports/my/latency/perCampaign',
            'twoStepVerifyUrl' => self::CONNECTURL.'/2step',
            'twoStepReportsUrl' => self::CONNECTURL.'/2step/reports',
            'twoStepReportsAppUrl' => self::CONNECTURL.'/2step/reports/applications'        
        );
        
        return $defaultUrl;
    }
      
   
}
