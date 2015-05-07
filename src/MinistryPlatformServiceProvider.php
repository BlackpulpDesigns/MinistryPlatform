<?php namespace Blackpulp\MinistryPlatform;

use \Illuminate\Support\ServiceProvider;

/**
 * The Laravel Service Provider
 *
 * Longer Description?
 * 
 * @author Ken Mulford <ken@blackpulp.com>
 * @category MinistryPlatform
 * @package  Blackpulp\MinistryPlatform
 * @version  1.0
 */

/**
 * This class handles the bulk of the MinistryPlatform API interactions.
 */
class MinistryPlatformServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = true;

  /**
   * Register bindings in the container.
   *
   * @return void
   */
  public function register()
  {
    $this->publishes([
      __DIR__.'/config/mp.php' => config_path('mp.php'),
    ]);
  }

}