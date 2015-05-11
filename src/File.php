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
 * This class handles user-specific interactions.
 */
class File
{   
  /**
   * The description stored on the new File record
   * @var string
   */
  public $description;

  /**
   * Stored whether the file is an image.
   * @var boolean
   */
  public $is_image;

  /**
   * The byte array of binary data for the file.
   * @var binary
   */
  public $binary;

  /**
   * The name of the file
   * @var string
   */
  public $name;

  /**
   * The MinistryPlatform Page ID the file is associated with
   * @var integer
   */
  public $page_id;

  /**
   * The record ID the file is associated with.
   * @var integer
   */
  public $record_id;

  /**
   * The number of pixels to resize the longest side of an image. 
   * Enter a value of 0 to keep the original dimensions.
   * @var integer
   */
  public $pixels;

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

  public function attach() {

    $response = $this->mp->attachFile($this);

    $this->guid = $response[0];
    $this->message = $response[2];

    return true;

  }

  public function getName() {

    return $this->name;

  }

  public function getMessage() {

    return $this->message;

  }

  public function getGuid() {

    return $this->guid;

  }

  public function makeDefault() {

    if($this->is_image) {

      $response = $this->mp->updateDefaultImage($this);

      $this->guid = $response[0];
      $this->message = $response[2];

      return true;

    }
    else {

      throw new MinistryPlatformException("File must be an image. Cannot make a non-image the default.");

    }

  }

}
