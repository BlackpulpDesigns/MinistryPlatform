<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform FindContact
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.1
 */

/**
 * This class handles the process of finding a Contact.
 */
class FindContact
{   

  /**
   * first name
   * @var string
   */
  protected $first_name;
  
  /**
   * last name
   * @var string
   */
  protected $last_name;

  /**
   * optional_fields
   * @var array associative array of all optional matching parameters
   */
  
  protected $optional_fields = [];

  /**
   * stored procedure used to match contacts
   * @var string
   */
  protected $sp = "api_Common_FindMatchingContact";

  /**
   * Results from the FindMatchingContact stored procedure
   * 
   * @var StoredProcedureResult
   */
  protected $matches;

  /**
   * Number of Matches
   *
   * @var int
   */
  protected $number_of_matches;


  /**
   * Construct Method
   * 
   * @param string $first_name
   * @param string $last_name 
   * @param array $optional_fields
   */
  public function __construct( $first_name, $last_name, $optional_fields) {
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->optional_fields = $optional_fields;
  }

  /**
   * Get the name of the current stored procedure
   * 
   * @return string
   */
  public function getStoredProcedure() {

    return $this->sp;

  }

  /**
   * Configure the stored procedure to be used for contact matching.
   * 
   * @param string $sp
   */
  public function setStoredProcedure($sp) {

    $this->sp = $sp;

    return $this;

  }

  /**
   * Returns the FindContact matches
   * 
   * @return StoredProcedureResult
   */
  public function getMatches() {

    if( !isset($this->matches) ) {
      
      $this->setMatches();

    }

    return $this->matches;

  }

  /**
   * Sets the $matches and $number_of_matches properties
   * 
   * Executes the FindMatchingContact stored procedure via MinistryPlatform.
   * Also allows users to override the default stored procedure and the
   * parameters passed to the stored procedure.
   *
   * @param string $sp The name of the "Find Matching Contacts" Stored Procedure
   *
   * @return $this
   */
  protected function setMatches() {

    $mp = new MinistryPlatform;

    $matching_fields = [
      "FirstName" => $this->first_name,
      "LastName" => $this->last_name
    ];

    $matching_fields = array_merge($matching_fields, $this->optional_fields);
    
    $this->matches = $mp->storedProcedure($this->sp, $matching_fields);

    if($this->matches->getTableCount() > 0) {

      foreach($this->matches->getTable(0) as $item) {
        if( is_array($item) ) {
          $this->number_of_matches = count( $this->matches->getTable(0) );
        }
        else {
          $this->number_of_matches = 1;
        }
        break;
      }

    } else {

      $this->number_of_matches = 0;

    }

    return $this;

  }

  /**
   * Gets the Number of Matches.
   *
   * @return int
   */
  public function getNumberOfMatches()
  {
    return $this->number_of_matches;
  }

  /**
   * Authenticate a uniquely matched user.
   *
   * As long as there is exactly one match *and* that match
   * has a valid User Account, go ahead and process 
   * authentication for them.
   * 
   * @return MinistryPlatform\Blackpulp\User
   */
  public function getUserAndAuthenticate() {

    if($this->getNumberOfMatches() === 1) {

      $user_data = $this->matches->getTable(0);

      if( isset($user_data['User_GUID']) && $user_data['User_GUID'] > 0 ) {
        $mp = new MinistryPlatform($user_data['User_Account']);
        return $mp->authenticateGuid($user_data['User_GUID']);
      }
      else {
        throw new MinistryPlatformException("The matched contact has no User Account.");
      }

    }
    else {

      throw new MinistryPlatformException("A single user was not returned. " .
        "Review the getMatches() method to determine your next steps.");

    }

  }
}
