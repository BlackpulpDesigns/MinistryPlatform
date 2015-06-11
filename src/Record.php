<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform Record
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  2.0
 */

/**
 * This class handles record-specific interactions.
 */
class Record
{   
  /**
   * An array of field names and their values.
   * 
   * @var array
   */
  protected $fields;


  /**
   * The associated Table
   * 
   * @var Table
   */
  protected $table;

  /**
   * The current record ID.
   * 
   * @var integer
   */
  protected $record_id = 0;

  /**
   * The message from the API save operation.
   * 
   * @var string
   */
  protected $message;
  
  /**
   * Instantiate a Record object.
   * 
   * @param Table  $table
   * @param array $fields
   */
  public function __construct(Table $table, $fields) {

    $this->table = $table;
    $this->fields = $fields;
    $this->setRecordId();

  }

  /**
   * Save a record to MinistryPlatform.
   *
   * @return Record $return
   */

  public function save() {
    $return = [];
    
    if( array_key_exists($this->table->getPrimaryKey(), $this->fields) ) {

      $return = $this->table->getMpInstance()->updateRecord($this);

    }

    else {

      $return = $this->table->getMpInstance()->addRecord($this);
      $key = $this->table->getPrimaryKey();
      $this->fields[$key] = $return[0];
    }

    $this->record_id = $return[0];
    $this->message = $return[2];

    return $this;
  }

  /**
   * Retrieve the current Record's ID.
   *
   * @return int
   */

  public function getId() {

    return $this->record_id;

  }

  /**
   * Return the save operation's message.
   *
   * @return string
   */

  public function getMessage() {

    return $this->message;

  }

  /**
   * Get the table name of the current record.
   * 
   * @return string
   */
  public function getTable() {

    return $this->table->getName();

  }

  /**
   * Get the array of fields of the current record.
   * 
   * @return array
   */
  public function getFields() {

    return $this->fields;

  }

  /**
   * Update the list of fields on the current record.
   * 
   * @param array $fields
   */
  public function setFields(array $fields) {

    $this->fields = $fields;

    return $this;

  }

  /**
   * Get the array of fields of the current record.
   *
   * @param string $name The name of the field being requested.
   * 
   * @return array
   */
  public function getField($name) {

    return $this->fields[$name];

  }

  /**
   * Update the list of fields on the current record.
   *
   * @param string $name The name of the field being updated.
   * @param string $value The value of the field being updated.
   * 
   * @param array $fields
   */
  public function setField($name, $value) {

    $this->fields[$name] = $value;

    return $this;

  }

  /**
   * Retrieve the name of the Primary_Key field
   * 
   * @return string
   */
  public function getPrimaryKey() {

    return $this->table->getPrimaryKey();

  }

  /**
   * Retrieve the current MinistryPlatform instance
   * 
   * @return MinistryPlatform
   */
  public function getMpInstance() {

    return $this->table->getMpInstance();

  }

  /**
   * Store the current Record ID.
   * 
   * Check the provided fields to see if we can identify 
   * the current Primary Key's value.
   *
   * @return void
   */
  protected function setRecordId() {

    if( array_key_exists($this->table->getPrimaryKey(), $this->fields) ) {

      $this->record_id = $this->fields[$this->table->getPrimaryKey()];

    }
    else {

      $this->record_id = 0;

    }

    return $this;

  }

  /**
   * Create a RecurringRecord object from the current record.
   * 
   * @return RecurringRecord
   */
  public function makeRecurring() {

    return new RecurringRecord($this);

  }

}
