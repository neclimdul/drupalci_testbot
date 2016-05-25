<?php
use DrupalCI\Plugin\Preprocess\definition\SyntaxCheck;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\SyntaxCheckDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class SyntaxCheckDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  /**
   * @param array|null $definition_overrides  Override value for the 'setup' key
   * @param boolean $value                    Whether Syntax Check should be enabled
   * @param array $expected_result            Expected Result
   *
   * @dataProvider provideSyntaxCheckDefinitionPreprocessorInputDefinitions
   */
  public function testSyntaxCheckDefinitionPreprocessor($definition_overrides, $value, $expected_result) {
    // Adds $definition['setup']['syntaxcheck'] = [TRUE|FALSE] line to the definition array.
    // SyntaxCheck should be the last element of the array after processing.

    $definition = $this->getDefinitionTemplate($definition_overrides);

    $plugin = new SyntaxCheck();
    $plugin->process($definition, $value);
    $this->assertEquals($expected_result, $definition['setup']);
  }

  public function provideSyntaxCheckDefinitionPreprocessorInputDefinitions() {
    return [
      // Test enabling syntaxcheck
      [['setup' => []], true, ['syntaxcheck' => "TRUE"]],
      // Test disabling syntaxcheck
      [['setup' => []], false, ['syntaxcheck' => "FALSE"]],
      // Test that syntaxcheck is the last element of the setup array after processing
      [['setup' => ['syntaxcheck' => "TRUE", 'otherkey' => 'otherkeyvalue']], true, ['otherkey' => 'otherkeyvalue', 'syntaxcheck' => "TRUE"]],
      // Prove that changing the key order results in a different array
      [['setup' => ['syntaxcheck' => "TRUE", 'otherkey' => 'otherkeyvalue']], true, ['syntaxcheck' => "TRUE", 'otherkey' => 'otherkeyvalue']],
    ];
  }
}