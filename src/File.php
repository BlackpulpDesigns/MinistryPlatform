<?php namespace Blackpulp\MinistryPlatform;

use Blackpulp\MinistryPlatform\MinistryPlatform;
use Blackpulp\MinistryPlatform\Exception as MinistryPlatformException;

/**
 * MinistryPlatform Files
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles File-based interactions.
 */
class File
{   
  /**
   * The description stored on the new File record
   * @var string
   */
  protected $description;

  /**
   * Stored whether the file is an image.
   * @var boolean
   */
  protected $is_image;

  /**
   * The byte array of binary data for the file.
   * @var binary
   */
  protected $binary;

  /**
   * The name of the file
   * @var string
   */
  protected $name;

  /**
   * The MinistryPlatform Page ID the file is associated with
   * @var integer
   */
  protected $page_id;

  /**
   * The record ID the file is associated with.
   * @var integer
   */
  protected $record_id;

  /**
   * The number of pixels to resize the longest side of an image. 
   * Enter a value of 0 to keep the original dimensions.
   * @var integer
   */
  protected $pixels;

  /**
   * An instance of MinistryPlatform
   * @var MinistryPlatform
   */
  protected $mp;

  /**
   * The message returned from the most recent API operation.
   * @var string
   */

  protected $message;

  /**
   * The GUID of the current file
   * @var guid
   */

  protected $guid;

  /**
   * Initialize a File object.
   * 
   * @param  string $file_name   The name of the file as it will be saved into MinistryPlatform.
   * @param  string $temp_name   The absolute physical path of the temp file's name.
   * @param  string $description  A description of the file.
   * @param  integer $page_id     The Page_ID value of the Record's Table in MinistryPlatform.
   * @param  integer $record_id   The ID of the Record the file will be attached to in MinistryPlatform.
   * @param  boolean $is_image    Simple boolean to determine whether the file is an image.
   * @param  integer $pixels      Number of pixels to resize the longest side of an image.
   *   Use 0 to retain the original dimensions.
   * @param MinistryPlatform $mp
   */
  public function __construct(
    $file_name,
    $temp_name,
    $description,
    $page_id,
    $record_id,
    $is_image,
    $pixels,
    $mp = null
  ) {

    $this->mp = is_null($mp) ? new MinistryPlatform() : $mp;
    $this->name = $file_name;
    $this->description = $description;
    $this->binary = file_get_contents($temp_name);
    $this->page_id = $page_id;
    $this->record_id = $record_id;
    $this->is_image = $is_image;
    $this->pixels = $pixels;
  }

  /**
   * Attach a file to a record.
   * 
   * @return [type] [description]
   */
  public function attach() {

    $response = $this->mp->attachFile($this);

    $this->guid = str_replace(".", "", $response[0]);
    $this->message = $response[2];

    return $this;

  }

  /**
   * Get the name of the current file
   * 
   * @return string
   */
  public function getName() {

    return $this->name;

  }

  /**
   * Whether the current file is an image.
   * 
   * @return boolean
   */
  public function getIsImage() {

    return $this->is_image;

  }

  /**
   * Get the file's description
   * 
   * @return string
   */
  public function getDescription() {

    return $this->description;

  }

  /**
   * Return the message from the last successful API call.
   * 
   * @return string
   */
  public function getMessage() {

    return $this->message;

  }

  /**
   * Retrieves the binary representation of the current file.
   * 
   * @return binary 
   */
  public function getBinary() {

    return $this->binary;

  }

  /**
   * Get the File's Page_ID
   * 
   * @return int
   */
  public function getPageId() {

    return $this->page_id;

  }

  /**
   * Return the file's associated Record ID
   * 
   * @return int
   */
  public function getRecordId() {

    return $this->record_id;

  }

  /**
   * Get the pixel resizing value
   * 
   * @return int
   */
  public function getPixels() {

    return $this->pixels;

  }

  /**
   * Retrieve the File GUID
   * @return string
   */
  public function getGuid() {

    return $this->guid;

  }

  /**
   * Make the current file the default for a record. Note that the 
   * is_image property must be true.
   * 
   * @return $this
   */
  public function makeDefault() {

    if($this->is_image) {

      $response = $this->mp->updateDefaultImage($this);

      $this->guid = $response[0];
      $this->message = $response[2];

    }
    else {

      throw new MinistryPlatformException("File must be an image. Cannot make a non-image the default.");

    }

    return $this;

  }

}
