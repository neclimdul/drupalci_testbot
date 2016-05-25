<?php

/**
 * @file
 * Base command class for Drupal CI.
 */

namespace DrupalCI\Console\Command;

use DrupalCI\Console\Output;
use DrupalCI\Providers\DockerServiceProvider;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use DrupalCI\Providers\ConsoleOutputServiceProvider;

/**
 * Just some helpful debugging stuff for now.
 */
class DrupalCICommandBase extends SymfonyCommand {

  /**
   * The container object.
   *
   * @var \Pimple\Container
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);
    // Perform some container set-up before command execution.
    $this->container = $this->getApplication()->getContainer();
    $this->container->register(new ConsoleOutputServiceProvider($output));
  }


  // Defaults for the underlying commands i.e. when commands run with --no-interaction or
  // when we are given options to setup containers.
  protected $default_build = array(
    'base'     => 'all',
    'web'      => 'drupalci/web-5.5',
    'database' => 'drupalci/mysql-5.5',
    'php'      => 'all'
  );

  protected function showArguments(InputInterface $input, OutputInterface $output) {
    $output->writeln('<info>Arguments:</info>');
    $items = $input->getArguments();
    foreach($items as $name=>$value) {
      $output->writeln(' ' . $name . ': ' . print_r($value, TRUE));
    }
    $output->writeln('<info>Options:</info>');
    $items = $input->getOptions();
    foreach($items as $name=>$value) {
      $output->writeln(' ' . $name . ': ' . print_r($value, TRUE));
    }
  }

  public function getDocker() {
    return $this->container['docker'];
  }

  public function getManager() {
    return $this->container['docker.image.manager'];
  }

}
