<?php

namespace DrupalCI\Tests\Console\Command\Config;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ConfigListCommandTest extends CommandTestBase {

  public function testList() {
    $c = $this->getConsoleApp();
    $command = $c->find('config:list');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName()]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('`Available config sets:`', $display);
  }

}
