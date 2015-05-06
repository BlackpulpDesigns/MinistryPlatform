<?php namespace Blackpulp\MinistryPlatform;

use \Config;

/**
 * MinistryPlatform Connection
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles the base connection information.
 */
class Connection
{
  /**
   * An absolute URL to the MinistryPlatform API endpoint.
   * @var string $wsdl
   */
  protected $wsdl;

  /**
   * The MinistryPlatform Domain GUID.
   * @var string $guid
   */
  protected $guid;

  /**
   * The MinistryPlatform API Password.
   * @var [type]
   */
  protected $pw;

  /**
   * The server name (mp.example.com).
   * @var string
   */
  protected $servername;

  /**
   * SOAP Client parameters.
   * @var array
   */
  protected $params;

  /**
   * Set up the Connection Object for use with the SoapClient class.
   *
   * @return void
   */
  function configureConnection()
  {
      
    $this->wsdl = Config::get("mp.wsdl");
    $this->guid = Config::get("mp.guid");
    $this->pw = Config::get("mp.password");
    $this->servername = Config::get("mp.name");

    $this->params = [
        'trace'        => true,
        'exceptions'   => 1
    ];
    return true;
  }

}
