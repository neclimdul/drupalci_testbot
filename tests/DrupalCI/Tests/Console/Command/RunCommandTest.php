<?php

namespace DrupalCI\Tests\Console\Command;

use DrupalCI\Tests\DrupalCIFunctionalTestBase;
use Symfony\Component\Console\Tester\CommandTester;

class RunCommandTest extends DrupalCIFunctionalTestBase {

  /**
   * {@inheritdoc}
   */
  protected $dciConfig = [
    'DCI_UseLocalCodebase=/tmp/drupal',
    'DCI_JobType=simpletest',
    'DCI_TestGroups=ban',
  ];

  public function testRun() {
    $c = $this->getConsoleApp();
    $command = $c->find('run');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('`Executing job with build ID:`', $display);
    $this->assertRegExp('`Loading DrupalCI platform default arguments:`', $display);
  }

}
