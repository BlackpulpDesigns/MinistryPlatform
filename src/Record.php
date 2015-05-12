<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform Records
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
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
   * The field name (not value) of the current table's Primary Key.
   * 
   * @var string
   */
  protected $primary_key;

  /**
   * The name of the current table
   * 
   * @var string
   */
  protected $table;

  /**
   * An instance of the MinistryPlatform object.
   * 
   * @var Blackpulp\Utilities\MinistryPlatform
   */
  protected $mp;

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
  
  public function __construct(MinistryPlatform $mp, $table, $primary_key, $fields) {

    $this->mp = $mp;
    $this->table = $table;
    $this->fields = $fields;
    $this->primary_key = $primary_key;
    $this->setRecordId();

  }

  /**
   * Save a record to MinistryPlatform.
   *
   * @return JSON $return
   */

  public function save() {
    $return = [];
    
    if( array_key_exists($this->primary_key, $this->fields) ) {

      $return = $this->mp->updateRecord($this);

    }

    else {

      $return = $this->mp->addRecord($this);

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

  public function getTable() {

    return $this->table;

  }

  public function getFields() {

    return $this->fields;

  }

  public function getPrimaryKey() {

    return $this->primary_key;

  }

  public function getMpInstance() {

    return $this->mp;

  }

  public function setFields(array $fields) {

    $this->fields = $fields;

    return $this;

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

    if( array_key_exists($this->primary_key, $this->fields) ) {

      $this->record_id = $this->fields[$this->primary_key];

    }

    return $this;

  }

  public function makeRecurring() {

    return new RecurringRecord($this);

  }

}
