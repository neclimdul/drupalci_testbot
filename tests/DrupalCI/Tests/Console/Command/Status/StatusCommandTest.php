<?php

namespace DrupalCI\Tests\Console\Command\Status;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;

class StatusCommandTest extends CommandTestBase {

  public function testStatus() {
    $c = $this->getConsoleApp();
    $command = $c->find('status');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);

    $this->assertRegExp('/Running Status Checks ... \nChecking Docker Version ... \n/', $commandTester->getDisplay(TRUE));
  }

}
