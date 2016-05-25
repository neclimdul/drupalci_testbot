<?php

namespace DrupalCI;

use Pimple\Container;

/**
 * Standardize the way the container is injected into objects.
 *
 * @see \DrupalCI\Injectable
 */
trait InjectableTrait {

  /**
   * @var \Pimple\Container
   */
  protected $container;

  public function setContainer(Container $container) {
    $this->container = $container;
  }

}

