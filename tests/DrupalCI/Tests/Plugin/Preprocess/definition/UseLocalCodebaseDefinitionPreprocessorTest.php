<?php
use DrupalCI\Plugin\Preprocess\definition\UseLocalCodebase;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\UseLocalCodebaseDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class UseLocalCodebaseDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  public function testUseLocalCodebaseDefinitionPreprocessor() {

    $definition = $this->getDefinitionTemplate();
    $source_directory = 'my/local/directory';
    $dci_variables = [];

    $expected_result = [
      'checkout' => [
        'protocol' => 'local',
        'source_dir' => 'my/local/directory'
      ]
    ];

    $plugin = new UseLocalCodebase();
    $plugin->process($definition, $source_directory, $dci_variables);
    $this->assertEquals($expected_result, $definition['setup']);
   }
}