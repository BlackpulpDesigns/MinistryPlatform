<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Record;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform FindOrCreateUser
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles the process of finding or creating a User.
 *
 * More specifically, it will ensure that any user found or created
 * through this object has a minimum number of associated records
 * and IDs that are key to most MinistryPlatform interactions.
 * 
 */
class FindOrCreateUser
{   

  protected $first_name;
  protected $last_name;
  protected $email;
  protected $phone;
  protected $dob;

  /**
   * Results from the FindMatchingContact stored procedure
   * 
   * @var Blackpulp/MinistryPlatform/StoredProcedureResult
   */
  protected $matches;

  /**
   * contact record
   * 
   * @var /Blackpulp/MinistryPlatform/Record
   */
  protected $contact;

  /**
   * household record
   * 
   * @var /Blackpulp/MinistryPlatform/Record
   */
  protected $household;

  /**
   * participant record
   * 
   * @var /Blackpulp/MinistryPlatform/Record
   */
  protected $participant;

  /**
   * donor record
   * 
   * @var /Blackpulp/MinistryPlatform/Record
   */
  protected $donor;

  
  public function __construct( $first_name, $last_name, $email='', $phone='', $dob=null) {
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->email = $email;
    $this->phone = $phone;
    $this->dob = $dob;
  }

  public function getMatches() {

    if( !isset($this->matches) ) {
      
      $this->setMatches();

    }

    return $this->matches;

  }

  protected function setMatches() {

    $mp = new MinistryPlatform;
    
    $this->matches = $mp->storedProcedure("api_blackpulp_FindMatchingContact", [
      "FirstName" => $this->first_name,
      "LastName" => $this->last_name,
      "EmailAddress" => $this->email,
      "Phone" => $this->phone,
      "DOB" => isset($this->dob) ? $mp->formatSoapDateTime($this->dob) : NULL;
    ]);

    return $this;

  }

}
