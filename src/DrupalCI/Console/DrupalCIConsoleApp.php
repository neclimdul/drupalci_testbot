<?php

/**
 * @file
 * Console application for Drupal CI.
 */

namespace DrupalCI\Console;

use DrupalCI\Console\Command\Init\InitBaseContainersCommand;
use DrupalCI\Console\Command\Init\InitDatabaseContainersCommand;
use DrupalCI\Console\Command\Init\InitDependenciesCommand;
use DrupalCI\Console\Command\Init\InitDockerCommand;
use DrupalCI\Console\Command\Init\InitWebContainersCommand;
use DrupalCI\Console\Command\Init\InitPhpContainersCommand;
use DrupalCI\InjectableTrait;
use Pimple\Container;
use Symfony\Component\Console\Application;
use DrupalCI\Console\Command\Init\InitAllCommand;
use DrupalCI\Console\Command\Init\InitConfigCommand;
use DrupalCI\Console\Command\BuildCommand;
use DrupalCI\Console\Command\PullCommand;
use DrupalCI\Console\Command\DockerRemoveCommand;
use DrupalCI\Console\Command\RunCommand;
use DrupalCI\Console\Command\Config\ConfigListCommand;
use DrupalCI\Console\Command\Config\ConfigLoadCommand;
use DrupalCI\Console\Command\Config\ConfigResetCommand;
use DrupalCI\Console\Command\Config\ConfigSaveCommand;
use DrupalCI\Console\Command\Config\ConfigSetCommand;
use DrupalCI\Console\Command\Config\ConfigShowCommand;
use DrupalCI\Console\Command\Config\ConfigClearCommand;
use DrupalCI\Console\Command\Status\StatusCommand;

class DrupalCIConsoleApp extends Application {

  use InjectableTrait;

  /**
   * Constructor.
   *
   * We'll store the injected container so that code with access to the app can
   * access it as needed.
   */
  public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN', Container $container) {
    parent::__construct($name, $version);
    $this->container = $container;
    $this->addCommands($container['commands']);
  }

  /**
   * Access the application object's container.
   *
   * @return \Pimple\Container
   */
  public function getContainer() {
    return $this->container;
  }

}
