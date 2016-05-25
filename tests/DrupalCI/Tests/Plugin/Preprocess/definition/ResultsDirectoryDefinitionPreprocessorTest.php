<?php
use DrupalCI\Plugin\Preprocess\definition\ResultsDirectory;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\ResultsDirectoryDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class ResultsDirectoryDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  public function testResultsDirectoryDefinitionPreprocessor() {

    $definition = $this->getDefinitionTemplate();
    $value = 'mydir';
    $dci_variables = [];
    $expected_result = ['mkdir' => ['mydir']];

    $plugin = new ResultsDirectory();
    $plugin->process($definition, $value, $dci_variables);
    $this->assertKeyImmediatelyPrecedesKey('pre-install', 'install', $definition);
    $this->assertEquals($expected_result, $definition['pre-install']);
   }
}