<?php

namespace DrupalCI\Tests\Console\Command;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class BuildCommandTest extends CommandTestBase {

  public function testStatus() {
    $c = $this->getConsoleApp();
    $command = $c->find('build');
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command' => $command->getName(), 'container_name' => ['non_existent_container']]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('`Executing build ...`', $display);
    $this->assertRegExp("`No 'non_existent_container' container found.  Skipping container build.`", $display);
  }

}
