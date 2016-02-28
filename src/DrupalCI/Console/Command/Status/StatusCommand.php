<?php

/**
 * @file
 * Command class for status.
 */

namespace DrupalCI\Console\Command\Status;

use DrupalCI\Console\Command\DrupalCICommandBase;
use DrupalCI\Console\Helpers\DrupalCIHelperBase;
use DrupalCI\Console\Helpers\DockerHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class StatusCommand extends DrupalCICommandBase {

  protected $errors = array();

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('status')
      ->setDescription('Shows the current status of the DrupalCI environment.');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $this->logger->info("Running Status Checks ...");

    # Check whether Docker is installed
    $docker = new DockerHelper();
    $docker->getStatus($input, $output);

    # Check whether base containers have been built and output list of available containers
    $this->containerStatus();

    # Check whether configuration sets have been created and output list of available config sets
    $this->configStatus();

    # Check whether testing dependencies (phpunit, etc) have been installed
    $this->dependencyStatus();

    # Output error counts and final status result
    $this->statusOutput();

  }

  protected function containerStatus() {
    # TODO: Check whether base containers have been built and output list of available containers
  }

  protected function configStatus() {
    # TODO: Check whether configuration sets have been created and output list of available config sets
  }

  protected function dependencyStatus() {
    # TODO: Check whether testing dependencies (phpunit, etc) have been installed
  }

  protected function statusOutput() {
    if (!empty($this->errors)) {
      $this->logger->error("Found " . count($this->errors) . " errors.");
      # TODO: Output count by error type.
    }
    else {
      $this->logger->info("No errors found!");
    }

  }

  // TODO: Check php configuration to ensure that $_ENV is populated (i.e. variables_order contains 'E').

}
