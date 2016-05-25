<?php
use DrupalCI\Plugin\Preprocess\definition\DrupalInstall;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\DrupalInstallDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class DrupalInstallDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  /**
   * @param array $dci_overrides     Array of key=>value pairs to use as overrides in $dci_variables
   * @param array $expected_result   Expected value of the $definition['install']['command'] array
   *
   * @dataProvider provideDrupalInstallDefinitionPreprocessorInputs
   */
  public function testDrupalInstallDefinitionPreprocessorUsingDrushInstall($dci_overrides, $expected_result) {

    $definition = $this->getDefinitionTemplate();
    $dci_variables = $this->getDCIVariables();

    $dci_variables = array_merge($dci_variables, $dci_overrides);

    $plugin = new DrupalInstall();

    $plugin->process($definition, 'drush', $dci_variables);
    $this->assertArrayHasKey('command', $definition['install']);
    $this->assertEquals($expected_result, $definition['install']['command']);
  }

  public function provideDrupalInstallDefinitionPreprocessorInputs() {
    $d8_result = [
      'cd /var/www/html && /.composer/vendor/bin/drush si -y --db-url=%DCI_DBurl% --clean-url=0 --account-name=admin --account-pass=drupal --account-mail=admin@example.com'
    ];
    $d7_result = [
      "cd /var/www/html && /.composer/vendor/bin/drush si -y --db-url=%DCI_DBurl% --clean-url=0 --account-name=admin --account-pass=drupal --account-mail=admin@example.com",
      "cd /var/www/html && /.composer/vendor/bin/drush vset simpletest_clear_results '0' 2>&1",
      "cd /var/www/html && /.composer/vendor/bin/drush vset simpletest_verbose '0' 2>&1",
      "cd /var/www/html && /.composer/vendor/bin/drush en -y simpletest 2>&1"
    ];
    return [
      // Test when DrupalCoreVersion is specified
      [['DCI_DrupalCoreVersion' => '7.x'], $d7_result],
      [['DCI_DrupalCoreVersion' => '8.x'], $d8_result],
      [['DCI_DrupalCoreVersion' => '8.0.x'], $d8_result],
      [['DCI_DrupalCoreVersion' => '8.1.x'], $d8_result],
      [['DCI_DrupalCoreVersion' => '8.2.x'], $d8_result],
      // Test when DCI_CoreBranch is specified
      [['DCI_CoreBranch' => '7.x'], $d7_result],
      [['DCI_CoreBranch' => '8.x'], $d8_result],
      [['DCI_CoreBranch' => '8.0.x'], $d8_result],
      [['DCI_CoreBranch' => '8.1.x'], $d8_result],
      [['DCI_CoreBranch' => '8.2.x'], $d8_result],
      // Test when neither is specified
      [[], $d8_result]
    ];
  }

}