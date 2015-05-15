<?php namespace Blackpulp\MinistryPlatform;

use StoredProcedureResult;
use Blackpulp\MinistryPlatform\MinistryPlatform;

/**
 * MinistryPlatform Config
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles retrieving configuration values from MinistryPlatform.
 *
 * It loads the configuration data from MinistryPlatform's "Configuration Settings"
 * table.
 * 
 */
class Config
{
  
  /**
   * the stored procedure used to fetch the configuration settings
   * 
   * @var string
   */
  protected $stored_procedure;
  
  /**
   * The array of configuration settings
   * 
   * @var array
   */
  protected $config = [];

  /**
   * The Application Code name that needs to be fetched from MinistryPlatform.
   * 
   * @var string
   */
  protected $application_code;


  /**
   * Instantiate a Config object and load the Configuration data.
   *
   * @param string $application_code The MP Application_Code value you want to get values for.
   * @param string $stored_procedure Provides an override option for the stored procedure used
   *   to fetch the config data from MinistryPlatform in the event you've written a custom 
   *   stored procedure.
   */
  public function __construct($application_code = "COMMON", $stored_procedure = "api_Common_GetConfigurationSettings") {

    $this->setApplicationCode($application_code);
    $this->setStoredProcedure($stored_procedure);
    $this->updateConfigurationSettings();

  }

  /**
   * Update $this->config by fetching fresh data from MinistryPlatform.
   * 
   * @return Config
   */
  public function updateConfigurationSettings() {

    $mp = new MinistryPlatform();

    $this->config = $mp->storedProcedure(
                          $this->stored_procedure, 
                          ['ApplicationCode' => $this->application_code]
                        )->getTable(0);

    return $this;
  }

  /**
   * Gets the stored procedure used to fetch the configuration settings.
   *
   * @return string
   */
  public function getStoredProcedure()
  {
    return $this->stored_procedure;
  }

  /**
   * Sets the stored procedure used to fetch the configuration settings.
   *
   * @param string $stored_procedure the stored procedure
   *
   * @return self
   */
  public function setStoredProcedure($stored_procedure)
  {
    $this->stored_procedure = $stored_procedure;

    return $this;
  }

  /**
   * Gets the The array of configuration settings.
   *
   * @return array
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Sets the The array of configuration settings.
   *
   * @param array $config the config
   *
   * @return self
   */
  protected function setConfig(array $config)
  {
    $this->config = $config;

    return $this;
  }

  /**
   * Gets the value of application_code.
   *
   * @return mixed
   */
  public function getApplicationCode()
  {
    return $this->application_code;
  }

  /**
   * Sets the value of application_code.
   *
   * @param mixed $application_code the application code
   *
   * @return self
   */
  public function setApplicationCode($application_code)
  {
    $this->application_code = strtoupper($application_code);

    return $this;
  }
}
