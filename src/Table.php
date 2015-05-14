<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Record;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

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
  protected $primary_key;
  protected $table_name;
  protected $mp;
  
  public function __construct(MinistryPlatform $mp, $table, $primary_key=false) {

    $this->mp = $mp;
    $this->table_name = $table;
    $this->primary_key = $primary_key;

  }

  public function getPrimaryKey() {

    return $this->primary_key;

  }

  public function getName() {

    return $this->table_name;

  }

  public function getMpInstance() {

    return $this->mp;

  }

  public function makeRecord($fields) {

    return new Record($this, $fields);

  }

}
