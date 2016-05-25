<?php

namespace DrupalCI\Tests\Console\Command\Config;

use DrupalCI\Tests\Console\Command\CommandTestBase;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigShowCommandTest extends CommandTestBase {

  public function testShow() {
    $c = $this->getConsoleApp();
    $command = $c->find('config:show');
    $commandTester = new CommandTester($command);
    $commandTester->execute([
      'command' => $command->getName(),
      'setting' => ['current'],
    ]);
    $display = $commandTester->getDisplay(TRUE);
    $this->assertRegExp('/Start config set: CURRENT DCI ENVIRONMENT/', $display);
    $this->assertRegExp('`Defined in ~/.drupalci/config:`', $display);
  }

}
