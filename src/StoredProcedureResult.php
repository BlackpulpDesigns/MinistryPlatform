<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform Stored Procedure Handling
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles interactions with Stored Procedure results.
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
  
  public function __construct($result) {

    $this->result = simplexml_load_string($result->ExecuteStoredProcedureResult->any);
    $this->checkForErrors();

    $this->schema = simplexml_load_string($result->ExecuteStoredProcedureResult->schema);
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

  }

  /**
   * Parse the XML into a series of arrays.
   */
  protected function setTables() {
    $tables = [];
    $table_index = 0;

    /** Iterate over tables */
    foreach($this->result->NewDataSet as $table) {
      
      $records = [];

      foreach((array)$table as $contents) {
        foreach((array)$contents as $field=>$record) {
          $records[$field] = is_numeric($record) ? (int)$record : $record;
        }
        $tables[$table_index] = $records;
        $table_index++;
      }
      
    }

    $this->tables = $tables;

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
