<?php
use DrupalCI\Plugin\Preprocess\definition\DBVersion;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\DBVersionDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class DBVersionDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  public function testDBVersionDefinitionPreprocessor() {

    $definition = $this->getDefinitionTemplate();
    $value = 'mysql';
    $dci_variables = [];
    $expected_result = ['mysql' => FALSE];

    $plugin = new DBVersion();
    $plugin->process($definition, $value, $dci_variables);
    $this->assertKeyImmediatelyPrecedesKey('dbcreate', 'install', $definition);
    $this->assertEquals($expected_result, $definition['dbcreate']);

   }
}