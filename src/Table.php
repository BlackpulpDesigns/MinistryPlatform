<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform;
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
  protected $table;
  protected $mp;
  
  public function __construct(MinistryPlatform $mp, $table, $primary_key=false) {

    $this->mp = $mp;
    $this->table = $table;
    $this->primary_key = $primary_key;

  }

  protected function record($fields) {

    return new Record($this->table, $this->primary_key, $fields);

  }

}
