<?php
/**
 *
 * Routee provides an API to create contacts so that you can later send SMS to them.
 *
 * @package Routee\lib\Api
 * @author kesavamoorthi<kesav@stallioni.com>,nandhakumar<nandha@stallioni.com>
 *
 * @return void
 */

namespace Routee\lib\Api;

use Routee\Core as core;

use Routee\lib\Api as auth;

use Routee\Exception as exceptions;

/**
 * Class Contacts
 *
 * Routee provides an API to create contacts so that you can later send SMS to them.
 *
 * Contacts are identified by a unique contact Id.
 *
 */

class Contacts 
{
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

            $this->contactUrl = $authentication->defaultRouteeConfigUrls->contactUrl;
            $this->contactBlackListUrl = $authentication->defaultRouteeConfigUrls->contactBlackListUrl;
            $this->contactLabelUrl = $authentication->defaultRouteeConfigUrls->contactLabelUrl;
            $this->contactGroupUrl = $authentication->defaultRouteeConfigUrls->contactGroupUrl;
            $this->contactGroupPageUrl = $authentication->defaultRouteeConfigUrls->contactGroupPageUrl;
            $this->contactGroupMergeUrl = $authentication->defaultRouteeConfigUrls->contactGroupMergeUrl;
            $this->contactGroupDifferenceeUrl = $authentication->defaultRouteeConfigUrls->contactGroupDifferenceeUrl;
            $this->contactGroupNameUrl = $authentication->defaultRouteeConfigUrls->contactGroupNameUrl;
            
        
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
     * Create a new contact or update it if it already exists.
     * @return string JSON
     * <code>
     * {
     *    "blacklistedServices": [
     *       "string"
     *    ],
     *    "country": "string",
     *    "labels": [
     *       {
     *          "name": "string",
     *          "type": "string",
     *          "value": "string"
     *       }
     *    ],
     *    "email": "string",
     *    "firstName": "string",
     *    "id": "string",
     *    "lastName": "string",
     *    "groups": [
     *       "string"
     *    ],
     *    "mobile": "string",
     *    "vip": "boolean"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * use Routee\lib\Api as api;
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * $contactResponse = new api\Contacts($config);
     * $data_contact = array(
     *    'firstName' => 'kesava',
     *    'lastName' => 'moorthi',
     *    'mobile' => '+919025060261',
     *    'vip' => 'true',
     * );
     * echo $contactResult = $contactResponse->createContacts( $data_contact );
     * </code>
     * @throws RouteeConnectionException
     */

    public function createContacts($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactUrl, 
        );                    
         
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
           $ex = new exceptions\RouteeConnectionException($e);            
           throw $ex;
        }

        return $response;
   }

    /**        
     * Delete multiple contacts that exist in the specified account.
     * @param array $data
     * @return string JSON
     * <code>
     * [  "string",  "string"  ]
     * </code>
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api; 
     * $data_del = array(
     *           array(
     *                'id' => '57b707690cf2121b4951e8e9'
     *               ),
     *           array(
     *                'id' => '57b703ea0cf2121b4951e8e2'
     *               ),
     *  );
     * $contactResponse = new api\Contacts($config);
     * echo $contactResult = $contactResponse->deleteMultipleContacts($data_del );
     * </code>
     * @throws RouteeConnectionException
     */

    public function deleteMultipleContacts($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactUrl, 
        );                    
         
        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }

        return $response;
    }

    /**        
     * Retrieve all the contacts of this account and sub-accounts in paged format.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------ | ------------ | ------------
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
     * size | Yes | The number of items to retrieve, default value is 10 | 1
     * @return string JSON
     * <code>
     * {  
     *    "totalPages":"number",
     *    "last":"boolean",
     *    "totalElements":"number",
     *    "first":"boolean",
     *    "numberOfElements":"number",
     *    "size":"number",
     *    "number":"number",
     *    "content":[  
     *       {    
     *          "blacklistedServices":[  
     *             "string"
     *          ],
     *          "country":"string",
     *          "labels":[  
     *             {  
     *                "name":"string",
     *                "type":"string",
     *                "value":"string"
     *             }
     *          ],
     *          "email":"string",
     *          "firstName":"string",
     *          "id":"string",
     *          "lastName":"string",
     *          "groups":[  
     *             "string"
     *          ],
     *          "mobile":"string",
     *          "vip":"boolean"
     *       }
     *    ]
     * }
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
     * content.blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * content.country | The country information of the contact.
     * content.labels | Contains the contact's labels with their respective values.
     * content.email | The e-mail address of the contact.
     * content.firstName | The first name of the contact.
     * content.id | The identification of the contact.
     * content.lastName | The last name of the contact.
     * content.groups | All contact groups (tags) that this contact belongs to.
     * content.mobile | The mobile number of the contact.
     * content.vip | Indicates whether the contact is treated as vip or not.
     * content.labels.name | The name of the label.
     * content.labels.type | The type of the label. Supported types are: Text or Number.
     * content.labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api; 
     * $contactResponse = new api\Contacts($config);
     * echo $contactResult = $contactResponse->retrieveAllContacts();
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAllContacts()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }         
        
        $executeData = array(
            'url'    => $this->contactUrl, 
        );                    
         
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }

        return $response;
    }
   
    /**        
     * Get the details of a specific contact providing the contact’s id.
     * @param array $data
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * id | The id of the contact.
     * @return string JSON
     * <code>
     * {  
     *    "blacklistedServices":[  
     *       "string"
     *    ],
     *    "country":"string",
     *    "labels":[  
     *       {  
     *          "name":"string",
     *          "type":"string",
     *          "value":"string"
     *       }
     *    ],
     *    "email":"string",
     *    "firstName":"string",
     *    "id":"string",
     *    "lastName":"string",
     *    "groups":[  
     *       "string"
     *    ],
     *    "mobile":"string",
     *    "vip":"boolean"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $contactid = '57c3dc0a0cf2d47a564a2af1';
     * echo $contactResult = $contactResponse->retrieveSingleContacts($contactid );
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveSingleContacts($data)
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }         
        
        $executeData = array(
            'url'    => $this->contactUrl.'/'.$data, 
        );                    
         
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }

        return $response;
    }

    /**        
     * Change the details of a specific contact providing the contact’s id.
     * @param string $contactId
     * @param $array data
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * id | The id of the contact to be updated.
     * @return string JSON
     * <code>
     * {  
     *    "blacklistedServices":[  
     *       "string"
     *    ],
     *    "country":"string",
     *    "labels":[  
     *       {  
     *          "name":"string",
     *          "type":"string",
     *          "value":"string"
     *       }
     *    ],
     *    "email":"string",
     *    "firstName":"string",
     *    "id":"string",
     *    "lastName":"string",
     *    "groups":[  
     *       "string"
     *    ],
     *    "mobile":"string",
     *    "vip":"boolean"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $contactid = '57c3dc0a0cf2d47a564a2af1';
     * $update_data = array(
	 *               'vip' => 'false',
	 *               'id'  => $contactid,
	 *               'mobile'=> '+917871962432',               
     *               'groups' => array('All','NotListed'),
	 *             );
	 * echo $contactResult = $contactResponse->updateContact($update_data,$contactid );
     * </code>
     * @throws RouteeConnectionException
     */

    public function updateContact($data,$contactId = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactUrl.'/'.$contactId, 
        );                    
         
        try { 
            $response = $this->executeCall($executeData,'PUT');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Delete an already existing contact providing the contact’s id.
     * @param string $contactId
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * id | The id of the contact to be deleted.
     * @return string JSON
     * <code>
     * {  
     *    "blacklistedServices":[  
     *       "string"
     *    ],
     *    "country":"string",
     *    "labels":[  
     *       {  
     *          "name":"string",
     *          "type":"string",
     *          "value":"string"
     *       }
     *    ],
     *    "email":"string",
     *    "firstName":"string",
     *    "id":"string",
     *    "lastName":"string",
     *    "groups":[  
     *       "string"
     *    ],
     *    "mobile":"string",
     *    "vip":"boolean"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $contactid = '57bc36470cf22cec5c422c9b';
     * echo $contactResult = $contactResponse->deleteContact($contactid );
     * </code>
     * @throws RouteeConnectionException
     */

    public function deleteContact($contactId = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
        
        $executeData = array(                
            'url'    =>$this->contactUrl.'/'.$contactId, 
        );                    
        
        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Check if there is an already existing contact with the same mobile providing the contact’s mobile.
     * @param string $mobileNumber
     * KEY | OPTIONAL | DESCRIPTION	| EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * value | No | The mobile used to check if a contact already exists | +306984512777
     * @return string JSON
     * <code>
     * </code>
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $mobNumber = '+919876543210';
     * echo $contactResult = $contactResponse->checkExistContact( $mobNumber );
     * </code>
     * @throws RouteeConnectionException
     */

    public function checkExistContact($mobileNumber = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }            
        
        $mobileData = array('value'=>$mobileNumber);

        $executeData = array(
            'httpResponse' => TRUE,               
            'url'    =>$this->contactUrl.'/mobile'. "?" . http_build_query($mobileData),                
        );                    
        
        try {

            $apiCallresponse = $this->executeCall($executeData,'HEAD');
            
            if($apiCallresponse != FALSE){
                $apiCallResponseDecode = json_decode($apiCallresponse);
                $callResponse = $apiCallResponseDecode->response;
                $response = (isset($callResponse->http_code) && $callResponse->http_code == 200) ? TRUE : FALSE;
            }
            else{
                $response = $apiCallresponse;
            }
               
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * Insert existing contacts to a service’s blacklist.
     * @param array $data
     * @param string $service
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * service | The service for which the contact will be added in blacklist.
     * @return string JSON
     * <code>
     * [
     *    {
     *       "blacklistedServices":[
     *          "string"
     *       ],
     *       "country":"string",
     *       "labels":[
     *          {
     *             "name":"string",
     *             "type":"string",
     *             "value":"string"
     *          }
     *       ],
     *       "email":"string",
     *       "firstName":"string",
     *       "id":"string",
     *       "lastName":"string",
     *       "groups":[
     *          "string"
     *       ],
     *       "mobile":"string",
     *       "vip":"boolean"
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data_service =  array(
	 *    array(
	 *        'id'     => '57c580380cf2d47a564ae51c',),
	 *    );
     * $service = 'Sms';
     * echo $contactResult = $contactResponse->addContactToBlackLists( $data_service,$service );
     * </code>
     * @throws RouteeConnectionException
     */

    public function addContactToBlackLists($data = array(),$service = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(
            'data'   => json_encode($data),         
            'url'    => $this->contactBlackListUrl.'/'.$service,                
        );                    

        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * Returns all the contacts which are blacklisted for the given service.
     * @param string $service
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * service | The service to get the blacklisted contacts.
     * @return string JSON
     * <code>
     * [  
     *    {  
     *       "blacklistedServices":[  
     *          "string"
     *       ],
     *       "country":"string",
     *       "labels":[  
     *          {  
     *             "name":"string",
     *             "type":"string",
     *             "value":"string"
     *          }
     *       ],
     *       "email":"string",
     *       "firstName":"string",
     *       "id":"string",
     *       "lastName":"string",
     *       "groups":[  
     *          "string"
     *       ],
     *       "mobile":"string",
     *       "vip":"boolean"
     *    }   
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * country | The country information of the contact.
     * email | The e-mail address of the contact.
     * firstName | The first name of the contact.
     * id | The identification of the contact.
     * lastName | The last name of the contact.
     * groups | All contact groups (tags) that this contact belongs to.
     * mobile | The mobile number of the contact.
     * vip | Indicates whether the contact is treated as vip or not.
     * labels | Contains the contact's labels with their respective values.
     * labels.name | The name of the label.
     * labels.type | The type of the label. Supported types are: Text or Number.
     * labels.value | The value of the label.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data_service =  'Sms';
     * echo $contactResult = $contactResponse->getBlackListsContactService( $data_service );
     * </code>
     * @throws RouteeConnectionException
     */

    public function getBlackListsContactService($service  = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(                 
             'url'    => $this->contactBlackListUrl.'/'.$service,                
            );                    
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Remove a group of existing contacts from the blacklist of a service
     * @param array $data
     * @param string $service
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * serviceName | The name of the service that the blacklist refers to (Sms, TwoStep)
     * @return string JSON
     * <code>
     * [
     *    {
     *      "updated":"number" 
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * updated | The number of affected contacts.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data =  array('testGroup');
     * $service = 'Sms';
     * echo $contactResult = $contactResponse->removeGroupOfContactFromBlackList( $data,$service );
     * </code>
     * @throws RouteeConnectionException
     */

    public function removeGroupOfContactFromBlackList($data = array(),$service = '')
    { 
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(
            'data'   => json_encode($data),             
            'url'    => $this->contactBlackListUrl.'/'.$service.'/groups',                
        ); 

        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Retrieve the account’s labels both default and custom.
     * @return string JSON
     * <code>
     * {
     *    "string":"string"
     * }
     * </code>
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * echo $contactResult = $contactResponse->retrieveAccountContactsLabels();
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAccountContactsLabels()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(                         
            'url'    => $this->contactLabelUrl,                
        ); 
                       
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Creates extra contact labels for the specified account. 
     * @return string JSON
     * <code>
     * [  
     *    {  
     *       "name":"string",
     *       "type":"string"
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the label.
     * type | The type of the label. Supported types are: Text or Number. Setting a label as Number will enable validation for it when changing the value for a contact.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data = array(
	 *     array(
     *        'name' => 'Company',
     *        'type' => 'Text',)
	 *     );
     * echo $contactResult = $contactResponse->createLabel($data);
     * </code>
     * @throws RouteeConnectionException
     */

    public function createLabel($data = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(
            'data'   => json_encode($data),                         
            'url'    => $this->contactLabelUrl,                
        ); 

        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * Retrieve the specified account’s groups of contacts.
     * @return string JSON
     * <code>
     * [  
     *    {  
     *       "name":"string",
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * echo $contactResult = $contactResponse->retrieveAccountGroups();
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAccountGroups()
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(                       
            'url'    => $this->contactGroupUrl,                
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Returns one of the groups that the account has created with the number of contacts in it.
     * @return string JSON
     * <code>
     * {
     *    "name":"string",
     *    "size":"number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The number of contact in the group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * echo $contactResult = $contactResponse->retrieveAccountGroupByName('All');
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAccountGroupByName($name = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        } 

        $executeData = array(                       
            'url'    => $this->contactGroupUrl.'/'.$name,                
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Retrieve a page of the specified account’s groups of contacts.
     * @param array $pageData
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
	 * size | Yes | The number of items to retrieve, default value is 10 | 1
     * @return string JSON
     * <code>
     * {  
     *    "totalPages":"number",
     *    "last":"boolean",
     *    "totalElements":"number",
     *    "first":"boolean",
     *    "numberOfElements":"number",
     *    "size":"number",
     *    "number":"number",
     *    "content":[  
     *       {  
     *          "name":"string",
     *          "numberOfContacts":"number"
     *       }
     *    ]
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * totalPages | The number of total pages.
     * last | Whether the current page is the last one.
     * totalElements | The total amount of elements for the current request.
     * first | Whether the current page is the first one.
     * numberOfElements | The number of elements currently on this page.
     * number | The requested page number.
     * size | The requested page size.
     * content | Contains the request results.
     * content.name | The name of the group.
     * content.numberOfContacts | content.numberOfContacts
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data_page = array(
     *           'page' => '0',
     *           'size' => '3',
	 *     );
     * echo $contactResult = $contactResponse->retrieveAccountGroupByPage($data_page);
     * </code>
     * @throws RouteeConnectionException
     */

    public function retrieveAccountGroupByPage($pageData = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }

        $executeData = array(                       
            'url'    => $this->contactGroupPageUrl."?". http_build_query($pageData),                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * The group can be either created empty or contacts can be added to it during the creation. 
     * The contacts can be added by using filters.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * name | No | The name of the group to be created.
     * strategy | Yes | Defines the way that the group should be populated. If not set (or set to 'None') the group will be empty. If it's set to 'Filtered' the group will be populated based on the filters provided. If it's set to 'All', the group will include all the contacts of the account.
     * filters	| Yes | The filters to apply and create the group from their result.
     * filters.fieldName | Yes | Defines the name of the field for this filter.
     * filters.searchTerm | Yes | Defines the search term to be used for the search.
     * @return string JSON
     * <code>
     * {  
     *    "name":"string",
     *    "size":"number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The size of the new group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data = array(
     *             'name' => 'TestGroup'.rand(0000,9999),         
	 *     );
     * echo $contactResult = $contactResponse->createGroup($data);
     * </code>
     * @throws RouteeConnectionException
     */

    public function createGroup($data = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactGroupUrl,                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
         return $response;
    }

    /**        
     * Deletes groups from the specified account.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION | EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * deleteContacts | Yes | If true, the contacts included in the group will also be deleted | true|false (default false)
     * @return string JSON
     * <code>
     * [  
     *    {  
     *       "name":"string",
     *       "deletedContacts":"boolean"
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group that was deleted.
     * deletedContacts | Indicates whether the contacts contained in the groups were also deleted or not.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data = array('AMD Telecom','one','two','three','one-two-three','difference');
     * echo $contactResult = $contactResponse->DeleteGroup($data);
     * </code>
     * @throws RouteeConnectionException
     */

    public function DeleteGroup($data = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
           'data'   => json_encode($data),                 
           'url'    => $this->contactGroupUrl,                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
    
        return $response;
    }

    /**        
     * Creates a new group as a merged result of multiple groups of contacts. 
     * Duplicate contacts will be added once in the new group. 
     * An extra group tag of the new merged group is added in every associated contact.
     * @param array $data
     * KEY |OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * name | No | The name of the group to be created.
     * groups | No | The names of the groups that will be merged.
     * @return string JSON
     * <code>
     * {  
     *    "name":"string",
     *    "size": "number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The size of the new group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data = array( 'name' => 'one-two-three','groups' => array('one','two','three'));
     * echo $contactResult = $contactResponse->mergeMultipleGroups($data);
     * </code>
     * @throws RouteeConnectionException
     */

    public function mergeMultipleGroups($data = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactGroupMergeUrl,                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * Creates a new group from the difference of contacts between the provided groups.
     * @param array $data
     * KEY | OPTIONAL | DESCRIPTION
     * ------------ | ------------- | -------------
     * name | No | The name of the group to be created.
     * groups | No | The names of the groups used to create the new group.
     * @return string JSON
     * <code>
     * {  
     *    "name":"string",
     *    "size": "number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The size of the new group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $data = array( 'name' => 'difference','groups' => array('one','two'));
     * echo $contactResult = $contactResponse->createGroupFromDifference($data);
     * </code>
     * @throws RouteeConnectionException
     */

    public function createGroupFromDifference($data = array())
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }

        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactGroupDifferenceeUrl,                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * View the contacts that a group is consisted of.
     * @param string $name
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * 
     * KEY | OPTIONAL | DESCRIPTION	| EXAMPLE
     * ------------ | ------------- | ------------- | -------------
     * page | Yes | The page number to retrieve, default value is 0 (meaning the first page) | 1
     * size | Yes | The number of items to retrieve, default value is 10 | 1
     * sort | Yes | The label name that will be used to sort the results | firstName
     * @return string JSON
     * <code>
     * {  
     *    "totalPages":"number",
     *    "last":"boolean",
     *    "totalElements":"number",
     *    "first":"boolean",
     *    "numberOfElements":"number",
     *    "size":"number",
     *    "number":"number",
     *    "content":[  
     *       {  
     *          "blacklistedServices":[  
     *             "string"
     *          ],
     *          "country":"string",
     *          "labels":[  
     *             {  
     *                "name":"string",
     *                "type":"string",
     *                "value":"string"
     *             }
     *          ],
     *          "email":"string",
     *          "firstName":"string",
     *          "id":"string",
     *          "lastName":"string",
     *          "groups":[  
     *             "string"
     *          ],
     *          "mobile":"string",
     *          "vip":"boolean"
     *       }
     *    ]
     * }
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
     * content.blacklistedServices | Defines all the services for which the contact has been blacklisted.
     * content.country | The country information of the contact.
     * content.labels | Contains the contact's labels with their respective values.
     * content.email | The e-mail address of the contact.
     * content.firstName | The first name of the contact.
     * content.id | The identification of the contact.
     * content.lastName | The last name of the contact.
     * content.groups | All contact groups (tags) that this contact belongs to.
     * content.mobile | The mobile number of the contact.
     * content.vip | Indicates whether the contact is treated as vip or not.
     * content.labels.name | The name of the label.
     * content.labels.type | The type of the label. Supported types are: Text or Number.
     * content.labels.value | content.labels.value.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $group_name = 'All';
     * echo $contactResult = $contactResponse->viewContactsByGroupName($group_name);
     * </code>
     * @throws RouteeConnectionException
     */

    public function viewContactsByGroupName($name = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
                
        $executeData = array(
            'url'    => str_replace('{group_name}', $name, $this->contactGroupNameUrl),
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'GET');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
       
        return $response;
    }

    /**        
     * Deletes the contacts that match the provided ids from the specified group
     * @param array $data
     * @param string $name
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * name | The name of the group which contains the contacts
     * @return string JSON
     * <code>
     * {  
     *    "name":"string",
     *    "size":"number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The new size of the group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $group_name = 'one';
     * $data = array('57bc372f0cf22cec5c422c9c');
     * echo $contactResult = $contactResponse->deleteContactsByGroupName($data,$group_name);
     * </code>
     * @throws RouteeConnectionException
     */

    public function deleteContactsByGroupName($data = array(), $name = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => str_replace('{group_name}', $name, $this->contactGroupNameUrl),                 
        ); 
                                      
        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Create a new contact or update it if it already exists.
     * @param array $data
     * @param string $name
     * NAME	| DESCRIPTION
     * ------------ | -------------
     * name | The name of the group
     * @return string JSON
     * <code>
     * {  
     *    "name":"string",
     *    "size":"number"
     * }
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * name | The name of the group.
     * size | The new size of the group.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $group_name = 'AMD Telecom';
     * $data = array('57c3dc0a0cf2d47a564a2af1');
     * echo $contactResult = $contactResponse->addContactsToGroupByName($data,$group_name);
     * </code>
     * @throws RouteeConnectionException
     */

    public function addContactsToGroupByName($data = array(), $name = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => str_replace('{group_name}', $name, $this->contactGroupNameUrl)
        ); 
                                  
        try { 
            $response = $this->executeCall($executeData,'POST');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**        
     * Extract existing contacts from a service’s blacklist.
     * @param array $data
     * @param string $service
     * NAME | DESCRIPTION
     * ------------ | -------------
     * service | The service for which the contact will be extracted from blacklist (Sms, TwoStep).
     * @return string JSON
     * <code>
     * [
     *    {
     *       "updated":"number" 
     *    }
     * ]
     * </code>
     * KEY | DESCRIPTION
     * ------------ | -------------
     * updated | The number of affected contacts.
     * @example
     * <code>
     * require_once __DIR__ . '/vendor/autoload.php';
     * $config = array(
     *     'application-id' => 'APPLICATION-ID', // ex : 57b5b7bde4b007f5ba82952b
     *     'application-secret' => 'APPLICATION-SECRET', // ex: 6k6sitDAXR
     * );
     * use Routee\lib\Api as api;
     * $contactResponse = new api\Contacts($config);
     * $service_name = 'Sms';
     * $data = array(array('id'=>'57c580380cf2d47a564ae51c'));
     * echo $contactResult = $contactResponse->removeContactsFromBlacklists($data,$service_name);
     * </code>
     * @throws RouteeConnectionException
     */

    public function removeContactsFromBlacklists($data = array() ,$service = '')
    {
        if(empty($this->accessToken)){
            return $this->returnResponse;
        }
        
        $executeData = array(
            'data'   => json_encode($data),
            'url'    => $this->contactBlackListUrl.'/'.$service,                 
        ); 
                               
        try { 
            $response = $this->executeCall($executeData,'DELETE');
        }catch(Exception $e) {
            $ex = new exceptions\RouteeConnectionException($e);            
            throw $ex;
        }
        
        return $response;
    }

    /**
     * Static function which reurns new TwoStep Object.
     * @param array $config
     * @return Accounts Object.
     * @throws RouteeConnectionException
     */
    static function getInstance( $config = array() )
    {
        return new self( $config );
    }

    /**
     * This function is used to execute the CURL.
     * @param array $exeData
     * @param string $httpMethod (GET,POST,PUT,DELETE)
     * @return string JSON
     */
    private function executeCall( $exeData = array(), $httpMethod = '')
    {
        $config  = isset( $exeData['config'] ) ? $exeData['config'] : $this->config;
        $url     = isset( $exeData['url'] ) ? $exeData['url'] : '';
        $headers = isset( $exeData['header'] ) ? $exeData['header'] : $this->header;
        $data    = isset( $exeData['data'] ) ? $exeData['data'] : '';
        $httpResponse = isset( $exeData['httpResponse'] ) ? $exeData['httpResponse'] : FALSE;
        $httpMethod = ( isset($httpMethod) && $httpMethod != '') ? $httpMethod : $this->httpMethod;
        
        $this->httpConfigObj->setUrl( $url );
        $this->httpConfigObj->setHeaders( $headers );
        $this->httpConfigObj->setMethod( $httpMethod );
        $this->httpConfigObj->setHttpResponse( $httpResponse );
        
        $this->httpConnObj = new core\RouteeHttpConnection( $this->httpConfigObj, $config );
        return $this->httpConnObj->execute( $data );
    }
  
}
