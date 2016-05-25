<?php

namespace DrupalCI\Providers;

use DrupalCI\Console\Command\BuildCommand;
use DrupalCI\Console\Command\Config\ConfigClearCommand;
use DrupalCI\Console\Command\Config\ConfigListCommand;
use DrupalCI\Console\Command\Config\ConfigLoadCommand;
use DrupalCI\Console\Command\Config\ConfigResetCommand;
use DrupalCI\Console\Command\Config\ConfigSaveCommand;
use DrupalCI\Console\Command\Config\ConfigSetCommand;
use DrupalCI\Console\Command\Config\ConfigShowCommand;
use DrupalCI\Console\Command\DockerRemoveCommand;
use DrupalCI\Console\Command\Init\InitAllCommand;
use DrupalCI\Console\Command\Init\InitBaseContainersCommand;
use DrupalCI\Console\Command\Init\InitConfigCommand;
use DrupalCI\Console\Command\Init\InitDatabaseContainersCommand;
use DrupalCI\Console\Command\Init\InitDependenciesCommand;
use DrupalCI\Console\Command\Init\InitDockerCommand;
use DrupalCI\Console\Command\Init\InitPhpContainersCommand;
use DrupalCI\Console\Command\Init\InitWebContainersCommand;
use DrupalCI\Console\Command\PullCommand;
use DrupalCI\Console\Command\RunCommand;
use DrupalCI\Console\Command\Status\StatusCommand;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConsoleCommandProvider implements ServiceProviderInterface {

  /**
   * Register all our console commands.
   *
   * @param Container $container
   */
  public function register(Container $container)
  {
    // Console Commands
    $container['command.status'] = function ($container) {
      return new StatusCommand();
    };
    $container['command.build'] = function ($container) {
      return new BuildCommand();
    };
    $container['command.pull'] = function ($container) {
      return new PullCommand();
    };
    $container['command.config.list'] = function ($container) {
      return new ConfigListCommand();
    };
    $container['command.config.load'] = function ($container) {
      return new ConfigLoadCommand();
    };
    $container['command.config.reset'] = function ($container) {
      return new ConfigResetCommand();
    };
    $container['command.config.save'] = function ($container) {
      return new ConfigSaveCommand();
    };
    $container['command.config.set'] = function ($container) {
      return new ConfigSetCommand();
    };
    $container['command.config.show'] = function ($container) {
      return new ConfigShowCommand();
    };
    $container['command.config.clear'] = function ($container) {
      return new ConfigClearCommand();
    };
    $container['command.docker.remove'] = function ($container) {
      return new DockerRemoveCommand();
    };
    $container['command.init.all'] = function ($container) {
      return new InitAllCommand();
    };
    $container['command.init.base'] = function ($container) {
      return new InitBaseContainersCommand();
    };
    $container['command.init.db'] = function ($container) {
      return new InitDatabaseContainersCommand();
    };
    $container['command.init.dependencies'] = function ($container) {
      return new InitDependenciesCommand();
    };
    $container['command.init.docker'] = function ($container) {
      return new InitDockerCommand();
    };
    $container['command.init.config'] = function ($container) {
      return new InitConfigCommand();
    };
    $container['command.init.web'] = function ($container) {
      return new InitWebContainersCommand();
    };
    $container['command.init.php'] = function ($container) {
      return new InitPhpContainersCommand();
    };
    $container['command.run'] = function ($container) {
      return new RunCommand();
    };

    $container['commands'] = function ($container) {
      return array(
        $container['command.status'],
        $container['command.build'],
        $container['command.pull'],
        $container['command.config.list'],
        $container['command.config.load'],
        $container['command.config.reset'],
        $container['command.config.save'],
        $container['command.config.set'],
        $container['command.config.show'],
        $container['command.config.clear'],
        $container['command.docker.remove'],
        $container['command.init.all'],
        $container['command.init.base'],
        $container['command.init.db'],
        $container['command.init.dependencies'],
        $container['command.init.docker'],
        $container['command.init.config'],
        $container['command.init.web'],
        $container['command.init.php'],
        $container['command.run']
      );
    };
  }

}
