<?php

namespace DrupalCI\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputServiceProvider implements ServiceProviderInterface {

  /**
   * The output object to inject.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;

  public function __construct(OutputInterface $output) {
    $this->output = $output;
  }

  public function register(Container $pimple) {
    $pimple['console.output'] = $this->output;
  }

}
