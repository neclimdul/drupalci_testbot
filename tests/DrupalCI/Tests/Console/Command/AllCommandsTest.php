<?php

namespace DrupalCI\Tests\Console\Command;

use DrupalCI\Tests\Console\Command\CommandTestBase;

/**
 * Tests dealing with all of the expected commands.
 *
 * @group Command
 */
class AllCommandsPresentTest extends CommandTestBase {

  public function provideCommandNames() {
    return [
      ['build'],
      ['config:clear'],
      ['config:list'],
      ['config:load'],
      ['config:reset'],
      ['config:save'],
      ['config:set'],
      ['config:show'],
      ['docker-rm'],
      ['init'],
      ['init:base'],
      ['init:config'],
      ['init:database'],
      ['init:dependencies'],
      ['init:docker'],
      ['init:php'],
      ['init:web'],
      ['pull'],
      ['run'],
      ['status'],
    ];
  }

  /**
   * Verify that we can find all commands on the app object.
   *
   * @coversNothing
   * @dataProvider provideCommandNames
   */
  public function testAllCommandsPresent($command_name) {
    $c = $this->getConsoleApp();
    // find() throws an exception if the name can't be found.
    $command = $c->find($command_name);
    $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $command);
  }

}
