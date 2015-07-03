<?php namespace Blackpulp\MinistryPlatform;

use StoredProcedureResult;

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
class MPConfig
{
  
  /**
   * the stored procedure used to fetch the configuration settings
   * 
   * @var string
   */
  protected $stored_procedure;

  /**
   * The Application Code name that needs to be fetched from MinistryPlatform.
   * 
   * @var string
   */
  protected $application_code;
  
  /**
   * The stored procedure result of configuration settings
   * 
   * @var StoredProcedureResult
   */
  protected $config;

  /**
   * An instance of the core MinistryPlatform object
   * 
   * @var MinistryPlatform
   */
  protected $mp;


  /**
   * Instantiate a Config object and load the Configuration data.
   *
   * @param MinistryPlatform $mp 
   * @param string $application_code The MP Application_Code value you want to get values for.
   * @param string $stored_procedure Provides an override option for the stored procedure used
   *   to fetch the config data from MinistryPlatform in the event you've written a custom 
   *   stored procedure.
   */
  public function __construct($mp, $application_code = "COMMON", $stored_procedure = "api_Common_GetConfigurationSettings") {

    $this->mp = $mp;
    $this->setApplicationCode($application_code);
    $this->setStoredProcedure($stored_procedure);

  }

  /**
   * Update $this->config by fetching fresh data from MinistryPlatform.
   * 
   * @return Config
   */
  public function updateConfigurationSettings() {

    $config = $this->mp->storedProcedure(
                          $this->stored_procedure, 
                          ['ApplicationCode' => $this->application_code]
                        );

    $this->config = $config;

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
   * Gets the configuration settings result object.
   *
   * @return array
   */
  public function getConfig()
  {
    if( !$this->config ) {
      $this->updateConfigurationSettings();
    }

    return $this->config;
  }

  /**
   * Sets the The array of configuration settings.
   *
   * @param StoredProcedureResult $config The config object
   *
   * @return self
   */
  protected function setConfig(StoredProcedureResult $config)
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

  /**
   * Retrieve the value of a specific configuration Key name
   * 
   * @param string $key The name of the Key
   * 
   * @return string
   */
  public function getConfigValue($key) {

    if(!$this->config) {
      $this->getConfig();
    }
    
    $config = $this->config->getTableKeyValuePair(0);

    return $config[$key];

  }

}
