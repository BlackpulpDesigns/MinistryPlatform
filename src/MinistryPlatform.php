<?php namespace Blackpulp\MinistryPlatform;

use \SoapClient;
use \SoapFault;
use Blackpulp\MinistryPlatform\MinistryPlatformException;
use Blackpulp\MinistryPlatform\CoreTool\Base as CoreTool;

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
   * An instance of the SOAP Client class
   * 
   * @var SoapClient
   */
  protected $client;

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

  /**
   * Initialize the MinistryPlatform Object
   *
   * @param string  $wsdl
   * @param string  $guid        
   * @param string  $password    
   * @param string  $server_name
   * @param integer $user_id the User_ID who is performing the API calls.
   *   This is used for Audit Logging in MinistryPlatform.
   */
  function __construct($wsdl, $guid, $password, $server_name, $user_id = 0) {

    $this->user_id = $user_id;
    parent::configureConnection($wsdl, $guid, $password, $server_name);

  }

  /**
   * Execute the API call.
   * 
   * Responsible for sending the MinistryPlatform API call and 
   * returning the response.
   * 
   * @param string $function  The name of the API method
   * @param array $parameters
   *
   * @return SimpleXMLObject
   */
  protected function execute($function, $parameters) { 
    
    try {

      $this->client = @new SoapClient($this->wsdl, $this->params);

    }
    catch(SoapFault $soap_error) {

      throw new MinistryPlatformException($soap_error->faultstring);
      exit;
    }

    try {
      $request = $this->client->__soapCall($function, [
        'parameters' => $parameters
      ]);
    }
    catch(SoapFault $soap_error) {
      $request = $soap_error->faultstring . "\r\nXML REQUEST: " . $this->client->__getLastRequest();
      throw new MinistryPlatformException($request);
    }
    return $request;
  }

  /**
   * Authenticate a user via MinistryPlatform.
   * 
   * @param string $user This is the MP username.
   * @param string $password This is the non-hashed password value.
   * 
   * @return User
   */

  public function authenticate( $username, $password ) {
    
    $function = 'AuthenticateUser';

    $parameters = array(
      'UserName'    => $username,
      'Password'    => $password,
      'ServerName'  => $this->servername
    );

    $response = $this->execute($function, $parameters);

    if($response->UserID == 0) {
      throw new MinistryPlatformException("Authentication failed. Please check your username and password.");
    }

    $this->user_id = (int)$response->UserID;

    return new User($this, $response, $username );
  }

  /**
   * Authenticate a user by using the User_GUID value instead of username + password
   * 
   * @param  string $guid
   * @return User
   */
  public function authenticateGuid($guid) {

    $function = "AuthenticateGUIDS";

    $parameters = array(
      "UserGUID" => $guid,
      "DomainGUID" => $this->guid
    );

    $response = $this->execute($function, $parameters);

    if($response->UserID == 0) {
      throw new MinistryPlatformException("Authentication failed. Please check the supplied unique identifier.");
    }

    $this->user_id = (int)$response->UserID;

    return new User($this, $response, "", $guid );
  }

  /**
   * Execute a stored procedure.
   *
   * @param string $sp The name of the stored procedure.
   * @param array $request An array of Stored Procedure parameters
   *
   * @return StoredProcedureResult
   */

  public function storedProcedure($sp, array $request = []) {

    $function = "ExecuteStoredProcedure";

    $parameters = [
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'StoredProcedureName' => $sp,
      'RequestString' => $this->ConvertToString($request)
    ];

    return new StoredProcedureResult( $this->execute($function, $parameters)->ExecuteStoredProcedureResult );
  }

  /**
   * Create a configuration object 
   * 
   * @param  string $application_code
   * @return MPConfig
   */
  public function makeConfiguration($application_code = "COMMON") {

    return new MPConfig($this, $application_code);

  }

  /**
   * Generate a base CoreTool object
   * 
   * @return CoreTool\Base
   */
  public function makeCoreTool() {

    return new CoreTool($this);

  }

  /**
   * Generate a FindContact instance
   * 
   * @param  string $first  
   * @param  string $last   
   * @param  array  $options
   * @return FindContact
   */
  public function findContact($first, $last, $options = array()) {

    return new FindContact($this, $first, $last, $options);

  }

  /**
   * Create a new MinistryPlatform record object.
   * 
   * @param string $table The name of the database table
   * @param string $primary_key The field name of the specified table's primary key.
   * @param array $fields An array of field names and their values
   *
   * @return Record
   */

  public function makeRecord($table, $primary_key, $fields) {

    return $this->makeTable($table, $primary_key)->makeRecord($fields);

  }

  /**
   * Create a MinistryPlatform Table object.
   * 
   * @param string $name The name of the database table
   * @param string $primary_key The field name of the specified table's primary key.
   *
   * @return Table
   */

  public function makeTable($name, $primary_key) {

    return new Table($this, $name, $primary_key);

  }
  /**
   * Create a MinistryPlatform File object. Used for attaching files.
   * 
   * @param  string $file_name   The name of the file as it will be saved into MinistryPlatform.
   * @param  string $temp_name   The absolute physical path of the temp file's name.
   * @param  string $description  A description of the file.
   * @param  integer $page_id     The Page_ID value of the Record's Table in MinistryPlatform.
   * @param  integer $record_id   The ID of the Record the file will be attached to in MinistryPlatform.
   * @param  boolean $is_image    Simple boolean to determine whether the file is an image.
   * @param  integer $pixels      Number of pixels to resize the longest side of an image.
   *   Use 0 to retain the original dimensions.
   *   
   * @return File
   */
  public function makeFile(
    $file_name,
    $temp_name,
    $description,
    $page_id,
    $record_id,
    $is_image,
    $pixels
  ) {

    return new File(
      $file_name,
      $temp_name,
      $description,
      $page_id,
      $record_id,
      $is_image,
      $pixels,
      $this
    );

  }

  /**
   * Add Record call to MinistryPlatform.
   * 
   * @param Record $record
   *
   * @return array
   */

  public function addRecord(Record $record) {

    $parameters = array(
      'GUID'             => $this->guid,
      'Password'         => $this->pw,
      'UserID'           => $this->user_id,
      'TableName'        => $record->getTable(),
      'PrimaryKeyField'  => $record->getPrimaryKey(),
      'RequestString'    => $this->ConvertToString($record->getFields())
    );

    $function = 'AddRecord';

    $response = $this->execute($function, $parameters);

    $results = $this->SplitToArray($response->AddRecordResult);

    if( $results[0] <= 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;
  }

  /**
   * Update Record call to MinistryPlatform.
   *  
   * @param Record $record
   *
   * @return array
   */

  public function updateRecord(Record $record) {

    $parameters = array(
      'GUID'             => $this->guid,
      'Password'         => $this->pw,
      'UserID'           => $this->user_id,
      'TableName'        => $record->getTable(),
      'PrimaryKeyField'  => $record->getPrimaryKey(),
      'RequestString'    => $this->ConvertToString($record->getFields())
    );

    $function = 'UpdateRecord';

    $response = $this->execute($function, $parameters);

    $results = $this->SplitToArray($response->UpdateRecordResult);
    if( $results[0] <= 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;
  }

  /**
   * Create a set of recurring records in MinistryPlatform
   * 
   * @param  RecurringRecord $recurring_record
   * @return array 
   */
  public function createRecurringSeries(RecurringRecord $recurring_record) {

    $parameters = array(
      'GUID'             => $this->guid,
      'Password'         => $this->pw,
      'UserID'           => $this->user_id,
      'TableName'        => $recurring_record->getRecord()->getTable(),
      'PrimaryKeyField'  => $recurring_record->getRecord()->getPrimaryKey(),
      'RequestString'    => $this->ConvertToString($recurring_record->getRecord()->getFields()),
      'csvSubTabsToCopy' => $recurring_record->getCsvSubTabIds(),
      'CopySubTabsFromRecordID' => $recurring_record->getSubTabSourceRecordId(),
      'Pattern' => $recurring_record->getPattern(),
      'Frequency' => $recurring_record->getFrequency(),
      'StartBy' => $this->formatSoapDateTime( $recurring_record->getStartBy() ),
      'EndBy' => $this->formatSoapDateTime( $recurring_record->getEndBy() ),
      'EndAfter' => $recurring_record->getEndAfter(),
      'SpecificDay' => $recurring_record->getSpecificDay(),
      'OrderDay' => $recurring_record->getOrderDay(),
      'SpecificMonth' => $recurring_record->getSpecificMonth(),
      'Sunday' => $recurring_record->getSunday(),
      'Monday' => $recurring_record->getMonday(),
      'Tuesday' => $recurring_record->getTuesday(),
      'Wednesday' => $recurring_record->getWednesday(),
      'Thursday' => $recurring_record->getThursday(),
      'Friday' => $recurring_record->getFriday(),
      'Saturday' => $recurring_record->getSaturday()
    );

    $function = 'AddRecurringRecords';

    $response = $this->execute($function, $parameters);

    $results = $this->SplitToArray($response->AddRecurringRecordsResult);
    if( $results[0] <= 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;

  }

  /**
   * Get the first date of a recurring series.
   * 
   * @param  RecurringRecord $recurring_record
   * @return array $results
   */
  public function getFirstDateInSeries(RecurringRecord $recurring_record) {

    $parameters = array(
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'Pattern' => $recurring_record->getPattern(),
      'Frequency' => $recurring_record->getFrequency(),
      'StartBy' => $this->formatSoapDateTime( $recurring_record->getStartBy() ),
      'EndBy' => $this->formatSoapDateTime( $recurring_record->getEndBy() ),
      'EndAfter' => $recurring_record->getEndAfter(),
      'SpecificDay' => $recurring_record->getSpecificDay(),
      'OrderDay' => $recurring_record->getOrderDay(),
      'SpecificMonth' => $recurring_record->getSpecificMonth(),
      'Sunday' => $recurring_record->getSunday(),
      'Monday' => $recurring_record->getMonday(),
      'Tuesday' => $recurring_record->getTuesday(),
      'Wednesday' => $recurring_record->getWednesday(),
      'Thursday' => $recurring_record->getThursday(),
      'Friday' => $recurring_record->getFriday(),
      'Saturday' => $recurring_record->getSaturday()
    );

    $function = 'GetFirstDateInSeries';

    $response = $this->execute($function, $parameters);

    $results = $response->GetFirstDateInSeriesResult;
    if( $results == 0 ) {

      throw new MinistryPlatformException($results[2], (int)$results[1]);

    }

    return $results;

  }

  /**
   * Get a list of each recurring date in a RecurringRecord object.
   *
   * Retrieve two tables of information from MinistryPlatform based on the values
   * of a RecurringRecord object. First, get back a table with a date 
   * representing every instance of the series. Second, get back a 
   * one-sentence description of the series (i.e. Every Tuesday from 
   * 1/1/2015 to 12/31/2015). 
   * 
   * @param  RecurringRecord $recurring_record
   * 
   * @return StoredProcedureResult 
   */
  public function getRecurringRecords(RecurringRecord $recurring_record) {

    $parameters = array(
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'Pattern' => $recurring_record->getPattern(),
      'Frequency' => $recurring_record->getFrequency(),
      'StartBy' => $this->formatSoapDateTime( $recurring_record->getStartBy() ),
      'EndBy' => $this->formatSoapDateTime( $recurring_record->getEndBy() ),
      'EndAfter' => $recurring_record->getEndAfter(),
      'SpecificDay' => $recurring_record->getSpecificDay(),
      'OrderDay' => $recurring_record->getOrderDay(),
      'SpecificMonth' => $recurring_record->getSpecificMonth(),
      'Sunday' => $recurring_record->getSunday(),
      'Monday' => $recurring_record->getMonday(),
      'Tuesday' => $recurring_record->getTuesday(),
      'Wednesday' => $recurring_record->getWednesday(),
      'Thursday' => $recurring_record->getThursday(),
      'Friday' => $recurring_record->getFriday(),
      'Saturday' => $recurring_record->getSaturday()
    );

    $function = 'GetRecurringRecords';

    return new StoredProcedureResult($this->execute($function, $parameters)->GetRecurringRecordsResult);

  }

  /**
   * GetUserInfo() API call
   *
   * @return StoredProcedureResult 
   */
  
  public function getUserInfo() {

    $function = "GetUserInfo";
    $parameters = [
      'GUID' => $this->guid,
      'Password' => $this->pw,
      'UserID' => $this->user_id
    ];

    return new StoredProcedureResult($this->execute($function, $parameters)->GetUserInfoResult);

  }

  /**
   * Attach a file to a record in MinistryPlatform.
   * 
   * @param  File $file
   * @return array
   */
  public function attachFile(File $file) {

    $parameters = array(
      'GUID'            => $this->guid,
      'Password'          => $this->pw,
      'FileContents'        => $file->getBinary(),
      'FileName'          => $file->getName(),
      'PageID'          => $file->getPageId(),
      'RecordID'          => $file->getRecordId(),
      'FileDescription'     => $file->getDescription(),
      'IsImage'         => $file->getIsImage(),
      'ResizeLongestDimension'  => $file->getPixels()
    );

    $function = 'AttachFile';

    $response = $this->SplitToArray( $this->execute($function, $parameters)->AttachFileResult );
    
    if( $response[0] == "0" ) {

      throw new MinistryPlatformException($response[2], (int)$response[1]);

    }

    return $response;
  }

  /**
   * If the current file is an image, makes that image the Record's default.
   * 
   * @param  File  $file
   * @return array
   */
  public function updateDefaultImage(File $file)
  {

    $parameters = array(
      'GUID'              => $this->guid,
      'Password'          => $this->pw,
      'UniqueName'        => $file->getGuid(),
      'PageID'            => $file->getPageId(),
      'RecordID'          => $file->getRecordId()
    );

    $function = 'UpdateDefaultImage';

    $response = $this->SplitToArray( $this->execute($function, $parameters)->UpdateDefaultImageResult );

    if( $response[0] == "0" ) {

      throw new MinistryPlatformException($response[2], (int)$response[1]);

    }

    return $response;
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

  /**
   * Convert the format of a datetime string.
   *
   * This is used to allow datetime strings to be sent from PHP to the SOAP
   * XML API where a datetime data type is required.
   * 
   * @param  string $timestamp Any datetime string
   * 
   * @return string  A different datetime string
   */
  public static function formatSoapDateTime($timestamp) {
    $timestamp = strtotime($timestamp);
    return date('Y-m-d', $timestamp) . 'T' . date('H:i:s', $timestamp);
  }

}