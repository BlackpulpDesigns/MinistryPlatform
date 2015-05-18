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

    $this->domain = isset($_GET['dg']) ? $_GET['dg'] : throw new Exception("Missing Required Parameter: Domain GUID.");
    $this->user_guid = isset($_GET['ug']) ? $_GET['ug'] : throw new Exception("Missing Required Parameter: User GUID.");
    $this->page_id = isset($_GET['pageID']) ? $_GET['pageID'] : throw new Exception("Missing Required Parameter: Page ID.");
    $this->record_id = isset($_GET['recordID']) ? $_GET['recordID'] : throw new Exception("Missing Required Parameter: Record ID.");
    $this->record_description = isset($_GET['recordDescription']) ? 
                                  $_GET['recordDescription'] : 
                                  throw new Exception("Missing Required Parameter: Record Description.");

    $this->selection_id = isset($_GET['s']) ? $_GET['s'] : throw new Exception("Missing Required Parameter: Selection ID.");
    $this->selection_count = isset($_GET['sc']) ? $_GET['sc'] : throw new Exception("Missing Required Parameter: Selection Count.");
    $this->selection = $this->getSelection();
    $this->pagination = isset($_GET['p']) ? $_GET['p'] : throw new Exception("Missing Required Parameter: Pagination.");
    $this->sort_order = isset($_GET['o']) ? $_GET['o'] : throw new Exception("Missing Required Parameter: Sort Order.");
    $this->query_string = isset($_GET['q']) ? $_GET['q'] : throw new Exception("Missing Required Parameter: Query String.");
    $this->view_id = isset($_GET['v']) ? $_GET['v'] : throw new Exception("Missing Required Parameter: View ID.");
    
    $this->user = new User($this->user_guid);

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