<?php namespace Blackpulp\MinistryPlatform\Laravel;

use \Illuminate\Support\ServiceProvider;

/**
 * The Laravel Service Provider
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @package  Blackpulp\MinistryPlatform
 * @version  1.0
 */

/**
 * The service provider allows Laravel to publish the config file
 * and handles dependency injection into the IoC Container.
 */
class MinistryPlatformServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;

  /**
   * Publish Configuration file.
   *
   * @return void
   */

  public function boot() {

    $this->publishes([
      __DIR__.'/config/mp.php' => config_path('mp.php'),
      __DIR__.'/config/churches/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX.php' => config_path('churches/XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX.php'),
    ]);

  }

  /**
   * Registers bindings in the container.
   * 
   * @return void
   */
  public function register() {
    $dg = $this->app->request->input('dg');

    if($dg) {
      // multi-tenant
      $this->mergeConfigFrom(__DIR__.'/config/churches/'.$dg.'php', 'mp');
    }
    else {
      // single tenant
      $this->mergeConfigFrom(__DIR__.'/config/mp.php', 'mp');
    }
    
    $this->_registerMinistryPlatform();

  }

  /**
   * Specifically registers the MinistryPlatform object in the IoC.
   * 
   * @return \Blackpulp\MinistryPlatform\MinistryPlatform
   */
  public function _registerMinistryPlatform() {

    $this->app->bindShared(
      'Blackpulp\MinistryPlatform\MinistryPlatform',
      function($app) {
        return new \Blackpulp\MinistryPlatform\MinistryPlatform (
                    $app->config->get('mp.wsdl'),
                    $app->config->get('mp.guid'),
                    $app->config->get('mp.password'),
                    $app->config->get('mp.name')
        );
      }
    );

  }

}