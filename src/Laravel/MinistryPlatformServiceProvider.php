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
  protected $defer = false;

  /**
   * Publish Configuration file.
   *
   * @return void
   */

  public function boot() {

    $this->publishes([
      __DIR__.'/config/mp.php' => config_path('mp.php'),
    ]);

  }

  /**
   * Registers bindings in the container.
   *
   * @return void
   */
  public function register() {

    $dg = strtoupper( $this->app->request->input('dg') );
    if($dg) {
      // multi-tenant
      $path = config_path('churches/'. $dg .'.php');
    }
    else {
      // single tenant
     $path = config_path('mp.php');
    }
    $this->app['config']->set('mp', require $path);

    $this->_registerMinistryPlatform();

  }

  /**
   * Specifically registers the MinistryPlatform object in the IoC.
   *
   * @return \Blackpulp\MinistryPlatform\MinistryPlatform
   */
  public function _registerMinistryPlatform() {

    $this->app->singleton(
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