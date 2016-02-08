<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatformException;

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
   * An object that contains helpful information about the user.
   *
   * @var StoredProcedureResult
   */
  protected $info;

  /**
   * All security roles for the authenticated user
   *
   * @var array
   */
  protected $roles = [];

  /**
   * All permitted Tools for the authenticated user
   *
   * @var array
   */
  protected $tools = [];

  /**
   * An instance of the core MinistryPlatform object
   *
   * @var MinistryPlatform
   */
  protected $mp;



  /**
   * Create the User object
   *
   * @param \SimpleXMLElement $user Response from MP Authenticate method
   * @param string $username If the MP AuthenticateUser() API call was
   * used, the username must be supplied.
   * @param string $user_guid If the MP AuthenticateGUIDs() API call was
   * used, the guid must be supplied instead.
   *
   * @return void
   */

  public function __construct($mp, $user, $username="", $user_guid="") {

    $this->mp = $mp;
    $this->id = (int)$user->UserID;
    $this->username = isset($user->UserName) ? (string)$user->UserName : $username;
    $this->display_name = (string)$user->DisplayName;
    $this->contact_id = (int)$user->ContactID;
    $this->user_guid = isset($user->UserGUID) ? (string)$user->UserGUID : $user_guid;
    $this->email = isset($user->ContactEmail) ? (string)$user->ContactEmail: null;
    $this->impersonate = (bool)$user->CanImpersonate;

    $this->getRoles();
  }

  public function setUserInfoFromGuid() {

    $this->info = $this->mp->storedProcedure("api_blackpulp_getUserInfoByGuid", [
      "GUID" => $this->user_guid
    ]);

    return $this;

  }

  /**
   * Get the user's User ID
   *
   * @return int
   */
  public function getId() {

    return $this->id;

  }

  /**
   * Get the user's Username
   *
   * @return string
   */
  public function getUserName() {

    return $this->username;

  }

  /**
   * Get the user's Contact ID
   *
   * @return int
   */
  public function getContactId() {

    return $this->contact_id;

  }

  /**
   * Get the user's Display Name
   *
   * @return string
   */
  public function getDisplayName() {

    return $this->display_name;

  }

  /**
   * Get the user's E-mail Address
   *
   * @return string
   */
  public function getEmail() {

    return $this->email;

  }

  /**
   * Get whether the user can impersonate other Platform users
   *
   * @return boolean
   */
  public function getImpersonate() {

    return $this->impersonate;

  }

  /**
   * Get the user's Security Roles
   *
   * @return array
   */
  public function getRoles() {

    if( empty( $this->roles ) ) {

      $this->setSecurityRolesAndTools();

    }

    return $this->roles;

  }

  /**
   * Get the user's Security Roles
   *
   * @return array
   */
  public function getTools() {

    if( empty( $this->tools ) ) {

      $this->setSecurityRolesAndTools();

    }

    return $this->tools;

  }

  /**
   * Sets security roles
   *
   * @return self
   */
  protected function setSecurityRolesAndTools() {

    $result = $this->mp->storedProcedure("api_blackpulp_GetUserRoles", ["UserID" => $this->id]);
    $this->roles = $result->getTableKeyValuePair(0);
    $this->tools = $result->getTableKeyValuePair(1);

    return $this;

  }

  /**
   * Get contact and user info about the user.
   *
   * Also returns lookup tables for Genders, Marital Statuses, Prefixes,
   * and Suffixes.
   *
   * @return StoredProcedureResult
   */
  public function getInfo() {

    if( get_class($this->info ) !== "Blackpulp\MinistryPlatform\StoredProcedureResult" ) {

      $this->setInfo();

    }

    return $this->info;

  }

  /**
   * Setter for the user info
   *
   * @return self
   */
  protected function setInfo() {

    $this->info = $this->mp->getUserInfo();

    return $this;

  }

  /**
   * Takes getInfo() and turns lookup arrays into Key-Value pairs.
   *
   * @return array
   */
  public function getFormattedTableData() {

    $info = $this->getInfo();

    $return = [];

    foreach($info->getTables() as $key=>$table) {
      $first_element = reset($table);
      if( is_array($first_element) ) {
        $return[$key] = $info->getTableKeyValuePair($key);
      }
      else {
        $return[$key] = $table;
      }

    }

    return $return;

  }

  public function getMpInstance() {

    return $this->mp;

  }

}
