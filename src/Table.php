<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Record;
use Blackpulp\MinistryPlatform\MinistryPlatformException;

/**
 * MinistryPlatform Table
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles table-specific interactions and instantiates records.
 */
class Table
{   
  /**
   * The name of the Primary Key field of the table.
   * 
   * @var string
   */
  protected $primary_key;

  /**
   * The name of the table
   * 
   * @var string
   */
  protected $table_name;
  
  /**
   * An instance of the core MinistryPlatform object
   * 
   * @var MinistryPlatform
   */
  protected $mp;

  /**
   * Instantiate a Table object
   * 
   * @param MinistryPlatform $mp 
   * @param string $table
   * @param string $primary_key
   */
  public function __construct(MinistryPlatform $mp, $table, $primary_key) {

    $this->mp = $mp;
    $this->table_name = $table;
    $this->primary_key = $primary_key;

  }

  /**
   * Retrieve the name of the current Primary Key field.
   * 
   * @return string
   */
  public function getPrimaryKey() {

    return $this->primary_key;

  }

  /**
   * Retrieve the name of the current table.
   * 
   * @return string
   */
  public function getName() {

    return $this->table_name;

  }

  /**
   * Retrieve the current MinistryPlatform instance
   * 
   * @return MinistryPlatform
   */
  public function getMpInstance() {

    return $this->mp;

  }

  /**
   * Create a new Record instance from the current Table instance.
   *
   * @param array $fields
   * 
   * @return Record
   */
  public function makeRecord($fields) {

    return new Record($this, $fields);

  }

}
