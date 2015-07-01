<?php namespace Blackpulp\MinistryPlatform;

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
   * @param  string $wsdl
   * @param  string $guid
   * @param  string $password
   * @param  string $servername
   * 
   * @return self             
   */
  function configureConnection($wsdl, $guid, $password, $servername)
  {
      
    $this->wsdl = $wsdl;
    $this->guid = $guid;
    $this->pw = $password;
    $this->servername = $servername;

    $this->params = [
        'trace'        => true,
        'exceptions'   => 1
    ];

    return $this;
  }

}
