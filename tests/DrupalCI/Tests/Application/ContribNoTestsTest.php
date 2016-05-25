<?php

namespace DrupalCI\Tests\Application;

use DrupalCI\Tests\DrupalCIFunctionalTestBase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Test what happens a contrib module has no tests.
 *
 * This test depends on the drupalci_d8_no_tests module which you can find here:
 * https://www.drupal.org/sandbox/mile23/2683655
 *
 * NOTE: This test assumes you have checked out Drupal 8.1.x branch into a
 * directory called /tmp/drupal, using a command like this:
 * git clone --branch 8.1.x https://git.drupal.org/project/drupal.git
 *
 * @group Application
 *
 * @see TESTING.md
 */
class ContribNoTestsTest extends DrupalCIFunctionalTestBase {

  /**
   * {@inheritdoc}
   */
  protected $dciConfig = [
    'DCI_UseLocalCodebase=/tmp/drupal',
    'DCI_CoreBranch=8.1.x',
    'DCI_AdditionalRepositories=git,https://git.drupal.org/sandbox/Mile23/2683655.git,8.x-1.x,modules/drupalci_d8_module_no_tests,1;',
    'DCI_DBVersion=mysql-5.5',
    'DCI_PHPVersion=5.5',
    'DCI_JobType=simpletest',
    'DCI_TestItem=directory:modules/drupalci_d8_module_no_tests',
    'DCI_ComposerInstall=true',
  ];

  public function testContribNoTests() {
    $app = $this->getConsoleApp();
    $options = ['interactive' => FALSE];

    $app_tester = new ApplicationTester($app);
    $app_tester->run([
      'command' => 'run',
    ], $options);

    $this->assertRegExp('/ERROR: No valid tests were specified./', $app_tester->getDisplay());
    $this->assertEquals(0, $app_tester->getStatusCode());
  }

}
