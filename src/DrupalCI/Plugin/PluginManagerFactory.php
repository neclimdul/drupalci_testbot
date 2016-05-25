<?php

namespace DrupalCI\Plugin;

use Pimple\Container;

class PluginManagerFactory {
  protected $container;

  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function create($plugin_type) {
    return new PluginManager($plugin_type, $this->container);
  }
}
