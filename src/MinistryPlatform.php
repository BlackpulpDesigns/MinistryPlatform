<?php namespace Blackpulp\MinistryPlatform;

use \Log;
use \SoapClient;
use \SoapFault;
use Blackpulp\MinistryPlatform\Connection;
use Blackpulp\MinistryPlatform\Record;
use Blackpulp\MinistryPlatform\User;
use Blackpulp\MinistryPlatform\StoredProcedureResult;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * The primary class to interact with the MinistryPlatform SOAP XML API.
 *
 * Longer Description?
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @package  Blackpulp\MinistryPlatform
 * @version  1.0
 */
/**
 * This class handles the bulk of the MinistryPlatform API interactions.
 */
class MinistryPlatform extends Connection {

  /**
   * Array of Parameters that will be passed to the API Call.
   * 
   * @var array
   */
  protected $parameters = [];

  /**
   * An instance of the SOAP Client class
   * 
   * @var SoapClient
   */
  protected $client;

  /**
   * The MinistryPlatform API function name.
   * 
   * @var string
   */
  protected $function;

  /**
   * An array of any errors returned by the API call.
   * 
   * @var array
   */
  protected $errors = [];

  /**
   * An array containing the results of an API call.
   * 
   * @var array
   */
  protected $results = [];

  /**
   * The ID of the authenticated MP User. Used for audit logging.
   * 
   * @var integer
   */
  protected $user_id;

  function __construct($user_id = 0) {

    $this->user_id = $user_id;
    parent::configureConnection();

  }

  /**
   * 
   */
  
  public function setFunction($function) {

    $this->function = $function;

  }

  public function setParameters(array $parameters) {

    $this->parameters = $parameters;

  }

  /**
   * Execute the API call.
   * 
   * Responsible for sending the MinistryPlatform API call and 
   * returning the response.
   *
   * @return SimpleXMLObject
   */

  protected function execute() { 
    
    try {

      $this->client = @new SoapClient($this->wsdl, $this->params);

    }
    catch(SoapFault $soap_error) {

      Log::error($soap_error->faultstring);
      return false;
      exit;
    }

    try {
      $request = $this->client->__soapCall($this->function, [
        'parameters' => $this->parameters
      ]);
    }
    catch(SoapFault $soap_error) {
      Log::error($soap_error->faultstring);
      $request = false;
    }
    return $request;
  }

  /**
   * Authenticate a user via MinistryPlatform.
   * 
   * @param string $user This is the MP username.
   * @param string $password This is the non-hashed password value.
   * 
   * @return Blackpulp\MinistryPlatform\User
   */

  public function authenticate( $username, $password ) {
    
    $this->function = 'AuthenticateUser';

    $this->parameters = array(
      'UserName'    => $username,
      'Password'    => $password,
      'ServerName'  => $this->servername
    );

    $response = $this->execute();

    if($response->UserID == 0) {
      throw new MinistryPlatformException("Authentication failed. Please check your username and password.");
    }

    $this->user_id = (int)$response->UserID;

    return new User($response, $username);
  }

  /**
   * Execute a stored procedure.
   *
   * @param string $sp The name of the stored procedure.
   * @param array $request An array of Stored Procedure parameters
   *
   * @return Blackpulp\MinistryPlatform\StoredProcedureResult
   */

  public function storedProcedure($sp, array $request = []) {

    $this->function = "ExecuteStoredProcedure";

    $this->parameters = [
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'StoredProcedureName' => $sp,
      'RequestString' => $this->ConvertToString($request)
    ];

    return new StoredProcedureResult( $this->execute() );
  }

  /**
   * @param string $table The name of the database table
   * @param array $fields An array of field names and their values
   * @param string $primary_key The field name of the specified table's primary key.
   *
   * @return Blackpulp\MinistryPlatform\Record
   */

  public function record($table, $fields, $primary_key) {

    return new Record($this, $table, $fields, $primary_key);

  }

  /**
   * @param string $name The name of the database table
   * @param string $primary_key The field name of the specified table's primary key.
   *
   * @return Blackpulp\MinistryPlatform\Table
   */

  public function table($name, $primary_key) {

    return new Table($this, $name, $primary_key);

  }

  /**
   * Add Record call to MinistryPlatform.
   * 
   * @param string $table The name of the database table
   * @param string $primary_key The field name of the specified table's primary key.
   * @param array $fields An array of field names and their values
   *
   * @return array
   */

  public function addRecord($table, $primary_key, $fields) {

    $this->parameters = array(
      'GUID'             => $this->guid,
      'Password'         => $this->pw,
      'UserID'           => $this->user_id,
      'TableName'        => $table,
      'PrimaryKeyField'  => $primary_key,
      'RequestString'    => $this->ConvertToString($fields)
    );

    $this->function = 'AddRecord';

    $response = $this->execute();

    $results = $this->SplitToArray($response->AddRecordResult);

    if( $results[0] <= 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;
  }

  /**
   * Update Record call to MinistryPlatform.
   *  
   * @param string $table The name of the database table
   * @param string $primary_key The field name of the specified table's primary key.
   * @param array $fields An array of field names and their values
   *
   * @return array
   */

  public function updateRecord($table, $primary_key, $fields) {

    $this->parameters = array(
      'GUID'             => $this->guid,
      'Password'         => $this->pw,
      'UserID'           => $this->user_id,
      'TableName'        => $table,
      'PrimaryKeyField'  => $primary_key,
      'RequestString'    => $this->ConvertToString($fields)
    );

    $this->function = 'UpdateRecord';

    $response = $this->execute();
    $results = $this->SplitToArray($response->UpdateRecordResult);
    if( $results[0] <= 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;
  }

  /**
   * GetUserInfo() API call
   *
   * 
   */
  
  public function getUserInfo() {

    $this->function = "GetUserInfo";
    $this->parameters = [
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'UserID' => $this->user_id
    ];

    return $this->execute()->GetUserInfoResult;

  }

  /**
   * Convert an array to a request string.
   * 
   * This method converts arrays of data into a simple request string. In the 
   * process, it also preps strings for insertion into MP by replacing
   * specific characters with MP-approved equivalents.
   *
   * @param array $array
   *
   * @return string Request String in the format of foo=bar&foo2=bar2.
   */

  protected function ConvertToString($array) {
    $temp = array();
    foreach($array as $k=>$v) {
      $val = $v;
      $val = str_ireplace("&", "dp_Amp", $val);
      $val = str_ireplace("=", "dp_Equal", $val);
      $val = str_ireplace("#","dp_Pound",$val);
      $val = str_ireplace("?","dp_Qmark",$val);
      $temp[] = $k . "=" . $val;
    }
    $string = trim( implode( "&", $temp) );
    return $string;
  }

  /**
   * Convert a pipe delimited string to an array.
   * 
   * Several MinistryPlatform API calls return a pipe-delimited string. This
   * method is used to quickly split that string into an array.
   *
   * @param string $string The pipe delimited string.
   * 
   * @return array $array
   */
  
  public static function SplitToArray($string) {
    $array = explode("|",$string); // separates the pipe delimited response string into an array
    return $array; // [0] = new ID
  }

}