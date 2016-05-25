<?php

namespace DrupalCI;

use Pimple\Container;

/**
 * Allows classes to signal that they can receive container injection.
 *
 * @see \DrupalCI\InjectableTrait
 */
interface Injectable {

  /**
   * The container object.
   *
   * @param Pimple\Container $container
   */
  public function setContainer(Container $container);

}

