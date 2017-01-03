<?php
/**
 * Class Accounts
 * The account class used to get the information about balance,routee services etc.
 *
 * @package Routee\lib\Api
 * @author  kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */

namespace Routee\lib\Api;

use Routee as config; 

use Routee\Core as core;

use Routee\lib\Api as auth;

use Routee\Exception as exceptions;

/**
 * Class Accounts
 *
 * The account class used to get the information about balance,routee services etc.
 */

class Accounts
{
    /**
     * This is the default variable declaration for access token
     *
     * @var string
     */
    private $accessToken;

    /**
     * This is the default variable declaration response from authentication
     *
     * @var string
     */
    private $returnResponse;

    /**
     * Private variable having configuration data.
     * @var string
     */
    private $config;
      
    /**
     * Private variable containing core\RouteeHttpConfig object
     * Object used to set Headers before every request.
     * @var core\RouteeHttpConfig object
     */
    private $httpConfigObj;

    /**
     * Private variable containing core\RouteeHttpConnection object
     * Used for making HTTP Requests and process Response.
     * @var core\RouteeHttpConnection object
     */
    private $httpConnObj;

    /**
     * This is the default variable declaration for account balace URL.
     *
     * @var string
     */
    private $accountBalUrl;

    /**
     * Default Constructor
     * This function used to get the access token from the authenticate the user credentials
     * @throws RouteeConnectionException
     */
    
    function __construct($config = array())
    {          
        $this->config = $config;
        $this->httpMethod = 'GET';
        
        try
        {
            $authentication = new auth\Authorization();
            $authResponse   = $authentication->getAuthorization($this->config);
            $authDecodeResponse   = json_decode($authResponse);
            $this->returnResponse = $authResponse;
            
            $this->accountBalUrl = $authentication->defaultRouteeConfigUrls->accountBalUrl;
            $this->routeeServiceUrl =$authentication->defaultRouteeConfigUrls->routeeServiceUrl;
            $this->accountTransUrl = $authentication->defaultRouteeConfigUrls->accountTransUrl;
            $this->availBankAccountsUrl = $authentication->defaultRouteeConfigUrls->availBankAccountsUrl;
            
            if(isset($authDecodeResponse->status) &&  $authDecodeResponse->status == 401){
                $this->accessToken = '';
            }

            $this->httpConfigObj = new core\RouteeHttpConfig( null, $this->httpMethod, $this->config );
            $this->accessToken = isset($authDecodeResponse->access_token) ? $authDecodeResponse->access_token : '';      
            $this->header = array(
                'Content-Type'  => "application/json",
                'Authorization' => "Bearer {$this->accessToken}"       
            ); 
        }
        catch( Exception $e ){
            return new exceptions\RouteeConnectionException( $e );
        }
       
    }
   
    /**
     * Static function which reurns new TwoStep Object.
     * @param array $config
     * @return string JSON
     * @throws RouteeConnectionException
     */
    
    static function getInstance($config = array())
    {
        return new self($config);
    }
   
    /**
     * This function is used to execute the CURL.
     * 
     * @param array $exeData
     * @param string $httpMethod (GET,POST,PUT,DELETE)
     * @return string JSON
     * @throws RouteeConnectionException
     */
    
    private function executeCall( $exeData = array(), $httpMethod = '')
    {        
        $config  = isset( $exeData['config'] ) ? $exeData['config'] : $this->config;
        $url     = isset( $exeData['url'] ) ? $exeData['url'] : '';
        $headers = isset( $exeData['header'] ) ? $exeData['header'] : $this->header;
        $data    = isset( $exeData['data'] ) ? $exeData['data'] : '';
        
        $httpMethod = ( isset($httpMethod) && $httpMethod != '') ? $httpMethod : $this->httpMethod;
        $this->httpConfigObj->setUrl( $url );
        $this->httpConfigObj->setHeaders( $headers );
        $this->httpConfigObj->setMethod( $httpMethod );
        
        $this->httpConnObj = new core\RouteeHttpConnection( $this->httpConfigObj, $config );
        
        return $this->httpConnObj->execute( $data );
    }
    
    /**
     * returns your available balance. 
     * Routee will return the amount in the currency you have defined in your account's settings.
     * @return string JSON that indicates the account balance, or throws Exception
     * <code>
     * {
     *   "balance":"number",
     *    "currency":{
     *     "code":"string",
     *     "name":"string",
     *     "sign":"string"
     *    }
     *  }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * balance | The amount of the available balance.
     * currency | Contains information about the accounts selected currency.
     * currency.code | The currency code in ISO 4217 format.
     * currency.name | The currency name in english.
     * currency.sign | The sign of the currency.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $account = api\Accounts::getInstance($config);
     * echo $account->retrieveAccountBal();
     * </code> 
     * @throws RouteeConnectionException
     */

    public function retrieveAccountBal()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 
		    
        $executeData = array(
	          'url'    =>  $this->accountBalUrl
	      );
		
        return $this->executeCall( $executeData);		
    }
	
    /**
     * retrieve all the prices for all Routee services, you can also specify various filters in order to retrieve only 
     * the prices that are of interest.
     * @param array $param
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * mcc | Yes | The mcc to filter price results | 202
     * mnc | Yes | The mnc to filter price results | 01
     * service | Yes | The service to filter price results (it can accept Sms, TwoStep, Lookup) | Sms
     * currency | Yes | The currency code to retrieve the prices | EUR
     * @return string JSON that indicates the routee services, or throws Exception
     * <code>
     *   {
     *      "sms":[
     *         {
     *            "mcc":"string",
     *            "country":"string",
     *            "iso":"string",
     *            "networks":[
     *               {
     *                  "network":"string",
     *                  "mnc":"string",
     *                  "price":"number"
     *               }
     *            ]
     *         }
     *      ],
     *      "lookup":{
     *         "PerRequest":"number"
     *      },
     *      "twoStep":{
     *         "SmsVerification":"number",
     *         "VoiceVerification":"number"
     *      },
     *      "currency":{
     *         "code":"string",
     *         "name":"string",
     *         "sign":"string"
     *      }
     *   }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * sms | Contains all the prices for sms service (per mcc and mnc).
     * sms.mcc | The mobile country code.
     * sms.country | The country name.
     * sms.iso | The ISO-3166 alpha 2 code of the country.
     * sms.networks	| All the networks of the country each containing the price.
     * sms.networks.network	| The network name.
     * sms.networks.mnc	| The mobile network code.
     * sms.networks.price | The price for the specific network.
     * twoStep | Contains prices for the 2Step service.
     * twoStep.SmsVerification | Prices per successful verification when verification is sent through SMS.
     * twoStep.VoiceVerification | Prices per successful verification when verification is sent through Audio.
     * lookup | Contains prices for the lookup service.
     * lookup.PerRequest | Price per lookup request.
     * currency | The currency of all prices.
     * currency.code | The currency code in ISO 4217 format.
     * currency.name | The currency name in english.
     * currency.sign | The sign of the currency.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $account = api\Accounts::getInstance($config);
     * echo $account->retrieveRouteeServices();
     * </code>
     * @throws RouteeConnectionException 
     */

    public function retrieveRouteeServices()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }	

        $executeData = array(
            'url'    =>  $this->routeeServiceUrl
        );
        
        return $this->executeCall( $executeData);
    }

    /**
     * retrieveAccountBal can be used to retrieve the transactions of your account.
     * @param array $param
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * from | Yes | ISO-8601 date-time format | 2015-11-11T15:00Z
     * to | Yes | ISO-8601 date-time format | 2015-11-11T15:00Z
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
     * size | Yes | The number of items to retrieve, default value is 20 | 1
     * @return string JSON
     * <code>
     *  {
     *     "totalPages":"number",
     *     "totalElements":"number",
     *     "last":"boolean",
     *     "numberOfElements":"number",
     *     "first":"boolean",
     *     "size":"number",
     *     "number":"number",
     *     "content":[
     *        {
     *           "id":"string",
     *           "source":"string",
     *           "transactionType":"string",
     *           "amount":"number",
     *           "status":"string",
     *           "balanceBefore":"number",
     *           "balanceAfter":"number",
     *           "date":"date",
     *           "actions":[
     *              {
     *                 "id":"string",
     *                 "type":"string",
     *                 "amount":"number",
     *                 "date":"date",
     *                 "balanceBefore":"number",
     *                 "balanceAfter":"number",
     *                 "status":"string"
     *              }
     *           ]
     *        }
     *     ] 
     *  }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * totalPages | The number of total pages.
     * last | Whether the current page is the last one.
     * totalElements | The total amount of elements for the current search.
     * first | Whether the current page is the first one.
     * numberOfElements | The number of elements currently on this page.
     * number | The requested page number.
     * size | The requested page size.
     * content | Contains the search results.
     * content.id | The id of the transaction.
     * content.source | The source that created this transaction (can be PayPal, Bank).
     * content.transactionType | Always TopUp.
     * content.amount | The amount of this transaction.
     * content.status | 1.PendingNotCredited ,2.Pending ,3.Completed .
     * content.balanceBefore | The balance the account had before the transaction was processed.
     * content.balanceAfter | The balance of the account after the transaction, depending on the transaction status the balance might or might not be affected.
     * content.date | The date that the transaction was created.
     * content.actions | A list of all the transaction actions (see action type for more).
     * content.actions.id | The id of the action.
     * content.actions.type | Actions can be applied to transactions and affect the balance of the account depending on the transaction status. 1.Paid ,2.Credit ,3.ChangeStatus ,4.Remove ,5.Refund .
     * content.actions.amount | The amount of the action.
     * content.actions.date | The date that the action was created.
     * content.actions.balanceBefore | The balance the account had before the action was processed.
     * content.actions.balanceAfter | The balance of the account after the action, see action type to see how the balance might be affected.
     * content.actions.status | The status of the action (Pending, Completed).
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $account = api\Accounts::getInstance($config);
     * echo  $account->retrieveAccountTransactions();
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAccountTransactions()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }

        $executeData = array(
            'url'    =>  $this->accountTransUrl
        );

        return $this->executeCall( $executeData);
    }

    /**
     * retrieve the AMD billing details and the available bank accounts where you can transfer money.
     * @return string JSON
     * <code>
     *  {  
     *     "name":"string",
     *     "address":"string",
     *     "phone":"string",
     *     "vatId":"string",
     *     "email":"string",
     *     "banks":[  
     *        {  
     *           "name":"string",
     *           "address":"string",
     *           "number":"string",
     *           "iban":"string",
     *           "currency":"string",
     *           "minAmount":"number",
     *           "country":"string",
     *           "swiftCode":"string"
     *        }
     *     ]
     *  }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of AMD company.
     * address | The address of AMD company.
     * phone | The phone number of AMD company.
     * vatId | The vat id of AMD company.
     * email | The email of AMD company.
     * banks | A list with all available banks where you can transfer money.
     * bank.name | The name of the bank.
     * bank.country | The country of the bank.
     * bank.address | The address of the bank.
     * bank.swiftCode | The swift code of the bank.
     * bank.number | The number of our bank account.
     * bank.iban | The iban of our bank account.
     * bank.currency | The currency that will be used when sending money to this bank account.
     * bank.minAmount | The minimum amount of money to send to this bank account.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $account = api\Accounts::getInstance($config);
     * echo $account->retrieveAvailBankAccounts();
     * </code>
     * @throws RouteeConnectionException
     */
   
    public function retrieveAvailBankAccounts()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }	

        $executeData = array(
            'url'    =>  $this->availBankAccountsUrl
        );

        return $this->executeCall( $executeData);
     }
}
