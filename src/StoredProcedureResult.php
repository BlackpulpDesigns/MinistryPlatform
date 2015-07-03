<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;
use SimpleXMLElement;

/**
 * MinistryPlatform Stored Procedure Handling
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles interactions with Stored Procedure results.
 *
 * More specifically, it provides a unified object used to interact with table-based
 * results returned from various MinistryPlatform API calls.
 */

class StoredProcedureResult
{   
  /**
   * The full raw result.
   * 
   * @var SimpleXMLElement
   */
  protected $result;

  /**
   * The XML schema object returned. Not sure if this is useful or not..
   * 
   * @var SimpleXMLElement
   */
  protected $schema;

  /**
   * A simple count of all tables returned in the dataset.
   * 
   * @var int
   */
  protected $table_count;

  /**
   * An array representation of all tables of data returned by the stored procedure. 
   * 
   * @var array
   */
  protected $tables;
  
  /**
   * Initialize the object
   * 
   * @param SimpleXMLElement $result
   * 
   */
  public function __construct($result) {

    $this->result = simplexml_load_string($result->any);
    $this->checkForErrors();

    $this->schema = simplexml_load_string($result->schema);
    $this->setTableCount();
    $this->setTables();
    
  }

  /**
   * Get the number of tables in the current stored procedure result.
   *
   * @return int
   */
  public function getTableCount() {

    return $this->table_count;

  }

  /**
   * Retrieve the tables returned by the Stored Procedure.
   */
  public function getTables() {

    return $this->tables;

  }

  /**
   * Retrieve a single table.
   *
   * @param int $key
   *
   * @return array
   */
  public function getTable($key = 0) {

    if( $this->tables && isset($this->tables[$key]) ) {
      return $this->tables[$key];  
    }
    else {
      throw new MinistryPlatformException("The requested table does not exist.");
    }

  }

  /**
   * Associative array of values from a Stored Procedure Table.
   *
   * When a stored procedure table returns an array of arrays, this can be helpful
   * in retrieving only the first two fields of data as a key value pair. 
   * 
   * For example, an array that contains a Prefix_ID and Prefix_Name would
   * ideally provide an a value of $prefixes[Prefix_ID] => Prefix_Name. 
   * This is precisely what this method will do. Please note that it
   * will ignore and discard any additional fields that may be in
   * the array.
   *
   * @param integer $key The array key of the requested stored procedure table.
   *
   * @return array A $key->$value array.
   */

  public function getTableKeyValuePair($key) {

    if( !isset( $this->tables[$key] ) ) {

      return [];
      // throw new MinistryPlatformException("The requested table does not exist. Use getTables() to view all available tables.");

    }

    $table = $this->tables[$key];
    $fields = [];

    foreach($table as $key=>$field) {

      if( is_array($field) ) {

        $keys = array_keys($field);

        if( count($keys) > 1 ) {

          $fields[ $field[ $keys[0] ] ] = $field[ $keys[1] ];

        }
        else {

          $fields[] = $field[ $keys[0] ];

        }

      }
      else {

        $fields[$key] = $field;

      }

    }

    return $fields;

  }

  /**
   * Get the Raw response object.
   *
   * @return SimpleXMLElement
   */
  public function getRaw() {

    return $this->result;

  }

  /**
   * Get the Schema object.
   *
   * @return SimpleXMLElement
   */
  public function getSchema() {

    return $this->schema;

  }

  /**
   * Set the table count.
   */
  protected function setTableCount() {

    $this->table_count = count((array)$this->result->NewDataSet);

    return $this;

  }

  /**
   * Parse the XML into a series of arrays.
   */
  protected function setTables() {
    $tables = [];
    $table_index = 0;

    /** Iterate over tables */
    foreach($this->result->NewDataSet as $table) {

      foreach((array)$table as $contents) {
        
        $records = $this->processTableContents($contents);
        
        $tables[$table_index] = $records;
        $table_index++;

      }
      
    }

    $this->tables = $tables;

    return $this;

  }

  /**
   * Digest the table contents.
   *
   * Convert XML Element contents into one or more arrays of data.
   * 
   * @param  SimpleXMLElement $contents
   * @return array 
   */
  protected function processTableContents($contents) {

    $records = [];
    if( is_object($contents) && get_class($contents) == "SimpleXMLElement" ) {

      $records = $this->processXMLElement($contents);

    }
    else {

      foreach($contents as $record) {

        $records[] = $this->processXMLElement($record);

      }

    }

    return $records;

  }

  /**
   * Process a single Element into an array of elements.
   *
   * Also detects nested elements and processes those, and handles some data type detection.
   * 
   * @param  SimpleXMLElement $element
   * @return array
   */
  protected function processXMLElement(SimpleXMLElement $element) {

    $records = [];
    foreach((array)$element as $field=>$record) {
      $value = $record;
      
      // handle INT values
      if(is_numeric($record) ) {
        $value = (int)$record;
      }

      // handle Float values
      if(is_float($record) ) {
        $value = (float)$record;
      }

      // handle booleans
      if(is_bool($record) ) {
        $value = (bool)$record;
      }

      // handle SimpleXMLElement values
      if( is_object($record) && get_class($record) == "SimpleXMLElement" ) {
        $value = $this->processXMLElement($record);
      }

      $records[$field] = $value;

    }

    return $records;

  }

  /**
   * Error handling for Stored Procedure API calls.
   * 
   * Check to see if the API call returned an error. If so, throw 
   * an exception and prevent things form continuing on.
   */
  protected function checkForErrors() {

    if( isset( $this->result->NewDataSet->Table1->ErrorMessage ) ) {
      $msg = (string)$this->result->NewDataSet->Table1->ErrorMessage;
      $error = MinistryPlatform::SplitToArray($msg);
      throw new MinistryPlatformException( $error[2] );
    }

  }

}