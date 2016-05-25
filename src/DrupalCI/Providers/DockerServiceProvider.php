<?php

namespace DrupalCI\Providers;


use Docker\Docker;
use Docker\Http\DockerClient;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DockerServiceProvider implements ServiceProviderInterface{

  /**
   * Register all of our Docker managers.
   *
   * @param Container $container
   */
  public function register(Container $container) {

    // Parent Docker object
    $container['docker'] = function ($container) {
      return new Docker(DockerClient::createWithEnv());
    };

    // Docker httpClient
    $container['docker.client'] = function ($container) {
      /* @var DockerClient */
      return $container['docker']->getClient();
    };

    // Docker Container Image Manager
    $container['docker.image.manager'] = function ($container) {
      return $container['docker']->getImageManager();
    };
  }

}