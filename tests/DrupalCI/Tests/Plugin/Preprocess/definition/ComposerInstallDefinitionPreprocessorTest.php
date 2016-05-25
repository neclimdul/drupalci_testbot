<?php
use DrupalCI\Plugin\Preprocess\definition\ComposerInstall;
use DrupalCI\Plugin\Preprocess\definition\DrupalInstall;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\ComposerInstallDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class ComposerInstallDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{

  /**
   * @param string $value        The value of DCI_ComposerInstall
   *
   * @dataProvider provideComposerInstallDisabledDefinitionPreprocessorInputs
   */
  public function testComposerInstallDisabledDefinitionPreprocessor($value) {

    $definition = $expected = ['key1' => 'value1'];
    $dci_variables = [];
    $plugin = new ComposerInstall();

    $plugin->process($definition, $value, $dci_variables);
    $this->assertEquals($expected, $definition, "Definition is not modified");
  }

  public function provideComposerInstallDisabledDefinitionPreprocessorInputs() {
    return [
      ['false', 'abcde']
    ];
  }

  /**
   * @param array $definition               The definition array being processed
   * @param array $setup_key_precedes       Defines the key which the 'setup' key should be inserted before
   *
   *
   * @dataProvider provideComposerInstallEnabledDefinitionPreprocessorInputs
   */
  public function testComposerInstallEnabledDefinitionPreprocessor($definition, $setup_key_precedes) {

    $dci_variables = $this->getDCIVariables();
    $subset = ["install --prefer-dist --working-dir "];

    $plugin = new ComposerInstall();

    $plugin->process($definition, 'true', $dci_variables);

    $this->assertArrayHasKey('composer', $definition['setup']);
    $this->assertKeyImmediatelyPrecedesKey('setup', $setup_key_precedes, $definition);
    $this->assertArraySubset($subset, $definition['setup']['composer']);

  }

  public function provideComposerInstallEnabledDefinitionPreprocessorInputs() {
    list ($definition, $definition1, $definition2) = $this->getDefinitions();
    $expected_result = ["install --prefer-dist --working-dir "];
    return [
      // $definition['setup'] defined
      'already_defined' => [$definition, 'pre-install'],
      // $definition['setup'] not defined, 'install' present
      'install_present' => [$definition1, 'install'],
      // $definition['setup'] not defined, 'install' not present
      'neither_present' => [$definition2, 'execute'],
    ];
  }

  protected function getDefinitions() {
    // test when $definition['setup'] already exists
    $definition = $this->getDefinitionTemplate();
    $definition1 = $definition;
    $definition2 = $definition;
    // test when $definition['setup'] doesn't exist, but $definition['install'] does
    unset($definition1['setup']);
    // test when $definition['setup'] and $definition['install'] don't exist, but $definition['execute'] does
    unset($definition2['setup']);
    unset($definition2['install']);
    // TODO: Test when $definition['execute'] doesn't exist, once plugin contains this logic
    // unset($definition3['setup']);  unset($definition3['install']);  unset($definition3['execute']);
    // TODO: Test when $composer already exists (i.e. does not overwrite), once plugin contains this logic
    return [$definition, $definition1, $definition2];
  }
}