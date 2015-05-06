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
    
    if( in_array($this->primary_key, $this->fields) ) {

      $return = $this->mp->updateRecord($this->table, $this->primary_key, $this->fields);

    }

    else {

      $return = $this->mp->addRecord($this->table, $this->primary_key, $this->fields);

    }

    $this->record_id = $return[0];
    $this->message = $return[2];

    return json_encode($return, JSON_PRETTY_PRINT);

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
   * Store the current Record ID.
   * 
   * Check the provided fields to see if we can identify 
   * the current Primary Key's value.
   *
   * @return void
   */

  protected function setRecordId() {

    if( in_array($this->primary_key, $this->fields) ) {

      $this->record_id = $this->fields[$this->primary_key];

    }

  }

}
