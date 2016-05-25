<?php

 namespace DrupalCI\Tests\Plugin\Preprocess\variable;

use DrupalCI\Plugin\Preprocess\VariableInterface;

class VariablePreprocessorTestBase extends \PHPUnit_Framework_TestCase
{

  /**
   * @param string $plugin_name        Name of the plugin being tested
   * @param string $dci_variable       Original value of the target variable
   * @param string $plugin_input       Value passed into the plugin logic
   * @param array  $expected return    Expected return value of the $plugin->process() method
   *
   * @dataProvider provideBasicVariablePreprocessorInputs
   *
   * @return array
   */
  public function testBasicVariablePreprocessors($plugin_name, $dci_variable, $plugin_input, $expected_return) {
    $plugin = "\\DrupalCI\\Plugin\\Preprocess\\variable\\$plugin_name";
    /** @var VariableInterface $instance */
    $instance = new $plugin();
    $return_value = $instance->process($dci_variable, $plugin_input);
    $this->assertEquals($expected_return, $return_value);
  }

  public function provideBasicVariablePreprocessorInputs() {
    return [
      // DCI_Color
      ['Color', 'abc', 'true', 'abc --color'],
      ['Color', 'abc', 'false', 'abc'],
      ['Color', 'abc', '', 'abc'],
      ['Color', 'abc', 'abc', 'abc'],
      // DCI_Concurrency
      ['Concurrency', 'abc', '1', 'abc --concurrency 1'],
      ['Concurrency', 'abc', '2', 'abc --concurrency 2'],
      ['Concurrency', 'abc', '', 'abc --concurrency '],   // TODO: Fix logic for empty input
      // DCI_DieOnFail
      ['DieOnFail', 'abc', 'true', 'abc --die-on-fail'],
      ['DieOnFail', 'abc', 'false', 'abc'],
      ['DieOnFail', 'abc', 'abc', 'abc'],
      ['DieOnFail', 'abc', '', 'abc'],
      // DCI_PHPInterpreter
      ['PHPInterpreter', 'abc', '/mypath', 'abc --php /mypath'],
      ['PHPInterpreter', 'abc', 'mypath', 'abc --php mypath'],
      ['PHPInterpreter', 'abc', '', 'abc'],       // TODO: Fix logic for empty input
      // DCI_RunOptions
      ['RunOptions', 'abc', 'arg1', ' --arg1'],
      ['RunOptions', 'abc', 'arg1,', ' --arg1 '],    // TODO: Fix logic for trailing comma input
      ['RunOptions', 'abc', 'arg1;', ' --arg1'],
      ['RunOptions', 'abc', 'arg1,val1', ' --arg1 val1'],
      ['RunOptions', 'abc', 'arg1,val1;', ' --arg1 val1'],
      ['RunOptions', 'abc', 'arg1,val1;arg2', ' --arg1 val1 --arg2'],
      ['RunOptions', 'abc', 'arg1,val1;arg2,', ' --arg1 val1 --arg2 '],  // TODO: Fix logic for trailing comma input
      ['RunOptions', 'abc', 'arg1,val1;arg2,val2', ' --arg1 val1 --arg2 val2'],
      ['RunOptions', 'abc', 'arg1,val1;arg2,val2;', ' --arg1 val1 --arg2 val2'],
      ['RunOptions', 'abc', '', ''],
      // DCI_SQLite
      ['SQLite', 'abc', 'mydir', 'abc --sqlite mydir'],
      ['SQLite', 'abc', '\mydir', 'abc --sqlite \mydir'],
      ['SQLite', 'abc', '', 'abc'],
      // DCI_TestItem
      ['TestItem', 'abc', '', 'abc'],
      ['TestItem', 'abc', 'all', '--all'],
      ['TestItem', 'abc', 'module:mymodule', '--module mymodule'],
      ['TestItem', 'abc', 'class:myclass', '--class myclass'],
      ['TestItem', 'abc', 'file:myfile', '--file myfile'],
      ['TestItem', 'abc', 'directory:mydir', '--directory mydir'],
      ['TestItem', 'abc', 'invalid:entry', 'abc'],
      // DCI_Verbose
      ['Verbose', 'abc', 'true', 'abc --verbose'],
      ['Verbose', 'abc', 'false', 'abc'],
      ['Verbose', 'abc', 'abc', 'abc'],
      ['Verbose', 'abc', '', 'abc'],
      // DCI_XMLOutput
      ['XMLOutput', 'abc', 'myfile', 'abc --xml myfile'],
      ['XMLOutput', 'abc', '\myfile', 'abc --xml \myfile'],
      ['XMLOutput', 'abc', '', 'abc --xml '],     // TODO: Fix logic for empty input
    ];
  }


}