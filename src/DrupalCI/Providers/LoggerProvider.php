<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 2/26/2016
 * Time: 10:10 PM
 */

namespace DrupalCI\Providers;


use DrupalCI\Console\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerProvider implements ServiceProviderInterface {

  public function register(Container $container) {
    $container['logger'] = function (Container $container) {
      $verbosityLevelMap = array(
        LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::INFO   => OutputInterface::VERBOSITY_NORMAL,
      );
      return new ConsoleLogger($container['console.output'], $verbosityLevelMap);
    };
  }

}