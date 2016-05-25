<?php

namespace DrupalCI\Tests\Console\Command;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class DockerRemoveCommandTest extends CommandTestBase {

  public function testStatus() {
    $c = $this->getConsoleApp();
    $command = $c->find('docker-rm');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
      'command' => $command->getName(),
      'type' => 'illegal_container',
    ]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('`illegal_container is not a legal container type.\n`', $display);
  }

}
