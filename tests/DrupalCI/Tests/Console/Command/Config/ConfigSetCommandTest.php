<?php

namespace DrupalCI\Tests\Console\Command\Config;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigSetCommandTest extends CommandTestBase {
  /**
   * @group failing
   */
  public function testSet() {
    $c = $this->getConsoleApp();
    $command = $c->find('config:set');
    $commandTester = new CommandTester($command);

    $helper = $command->getHelper('question');
    $helper->setInputStream($this->getInputStream('yes\\n'));

    $commandTester->execute([
      'command' => $command->getName(),
      'assignment' => ['foof=foof'],
    ]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('/(Setting the value of the foof variable to foof)|(The foof variable already exists.)/', $display);
  }

}
