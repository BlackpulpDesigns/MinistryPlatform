<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform Users
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles user-specific interactions.
 */
class User
{   
  /**
   * User_ID
   * 
   * @var int
   */
  protected $id;

  /**
   * Username
   * 
   * @var string
   */
  protected $username;

  /**
   * Display Name
   *
   * @var  string
   */
  
  protected $display_name;

  /**
   * Contact_ID
   * 
   * @var int
   */
  protected $contact_id;

  /**
   * User_GUID
   * 
   * @var string
   */
  protected $user_guid;

  /**
   * Email_Address
   * 
   * @var string
   */
  protected $email;

  /**
   * Whether the user is allowed to impersonate other Platform users.
   * 
   * @var bool
   */
  protected $impersonate;
  
  /**
   * All security roles for the authenticated user
   * 
   * @var array
   */
  protected $roles = [];
  
  public function __construct($user, $username) {

    $this->id = (int)$user->UserID;
    $this->username = $username;
    $this->display_name = (string)$user->DisplayName;
    $this->contact_id = (int)$user->ContactID;
    $this->user_guid = (string)$user->UserGUID;
    $this->email = (string)$user->ContactEmail;
    $this->impersonate = (bool)$user->CanImpersonate;
    $this->getRoles();
  }

  public function getId() {

    return $this->id;

  }

  public function getUserName() {

    return $this->username;

  }

  public function getContactId() {

    return $this->contact_id;

  }

  public function getDisplayName() {

    return $this->display_name;

  }

  public function getEmail() {

    return $this->email;

  }

  public function getImpersonate() {

    return $this->impersonate;

  }

  public function getRoles() {

    if( empty( $this->roles ) ) {

      $this->setSecurityRoles();

    }

    return $this->roles;

  }


  protected function setSecurityRoles() {

    $mp = new MinistryPlatform($this->id);
    $result = $mp->storedProcedure("api_Common_GetUserRoles", ["UserID" => $this->id])->getTables();
    $roles = [];

    foreach($result[0] as $role) {

      $roles[] = $role['Role_ID'];

    }

    $this->roles = $roles;

  }

}
