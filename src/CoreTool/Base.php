<?php namespace Blackpulp\MinistryPlatform\CoreTool;

use Blackpulp\MinistryPlatform\MinistryPlatformException;
use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\User;

class Base {
  
  protected $domain; // string/GUID
  protected $user_guid; // string/GUID
  protected $user;
  protected $page_id; // integer
  protected $record_id; // integer
  protected $record_description; // string
  protected $selection_id; // integer
  protected $selection_count; // integer
  protected $pagination; // integer, index starts at 0
  protected $sort_order; // string
  protected $query_string; // string
  protected $view_id; // integer

  /**
   * Array of the user's current selection
   * @var array
   */
  protected $selection = [];
  
  /**
  * Create the base coreTool object
  * 
  * Set all query params as properties, create an MP instance so hitting the MP API is quicker to do
  */
  
  public function __construct()
  {

    $this->domain = $_GET['dg'];
    $this->user_guid = $_GET['ug'];
    $this->page_id = $_GET['pageID'];
    $this->record_id = $_GET['recordID'];
    $this->record_description = $_GET['recordDescription'];
    $this->selection_id = $_GET['s'];
    $this->selection_count = $_GET['sc'];
    $this->selection = $this->setSelection();
    $this->pagination = $_GET['p'];
    $this->sort_order = !empty($_GET['o']) ? $_GET['o'] : "";
    $this->query_string = !empty($_GET['q']) ? $_GET['q'] : "";
    $this->view_id = !empty($_GET['v']) ? $_GET['v'] : 0;
    
    $this->user = new User( $this->user_guid );

    $this->mp = new MinistryPlatform( $this->user->getId() );

  }
  
  /**
   * @Description
   * Retrieves the current selection - if one exists
   */
  
  public function setSelection() {

    if(is_numeric($this->selection_count) && $this->selection_count > 0)
    {
      $sp = "api_Common_GetSelection";
      $params = [
        "UserID" => $this->user->getId()
        ,"PageID" => $this->page_id
        ,"SelectionID" => $this->selection_id
      ];

      $this->selection = $this->mp->storedProcedure($sp, $params)->getTable(0);

    }

    return $this;
  }
  
}