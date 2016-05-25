<?php

namespace DrupalCI\Providers;

use DrupalCI\Console\DrupalCIConsoleApp;
use DrupalCI\Plugin\PluginManagerFactory;
use DrupalCI\Providers\DockerServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Registers application-level services.
 */
class DrupalCIServiceProvider implements ServiceProviderInterface {

  /**
    * Register all our app-level services.
    *
    * @param Container $container
    */
  public function register(Container $container) {
    $container->register(new DockerServiceProvider());
    $container['console'] = function ($container) {
      return new DrupalCIConsoleApp('DrupalCI - CommandLine', '0.2', $container);
    };
    $container['plugin.manager.factory'] = function ($container) {
      return new PluginManagerFactory($container);
    };
  }

}
