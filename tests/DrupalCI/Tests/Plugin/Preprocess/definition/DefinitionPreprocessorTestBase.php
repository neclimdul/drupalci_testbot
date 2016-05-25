<?php

 namespace DrupalCI\Tests\Plugin\Preprocess\definition;

class DefinitionPreprocessorTestBase extends \PHPUnit_Framework_TestCase
{

  /**
   * @param array $key_overrides    An array of key=>value pairs to be used as overrides of the default template
   *
   * @return array
   */
  public function getDefinitionTemplate($key_overrides = [])
  {
    $definition = [
      'environment' => [
        'db' => ['%DCI_DBVersion%'],
        'web' => ['%DCI_PHPVersion%'],
      ],
      'setup' => [
        'checkout' => [
          'protocol' => 'git',
          'repo' => '%DCI_CoreRepository%',
          'branch' => '%DCI_CoreBranch%',
          'depth' => '%DCI_GitCheckoutDepth%',
          'checkout_dir' => '.',
          'commit_hash' => '%DCI_GitCommitHash%',
        ],
      ],
      'pre-install' => [],
      'install' => [],
      'execute' => [
        'command' => ['my_command'],
        'testcommand' => ['my_test_command'],
      ],
      'publish' => [],
    ];

    $template = array_merge($definition, $key_overrides);

    return $template;
  }

  /**
   * @return array
   */
  public function getDCIVariables() {
    return [];
  }

  /**
   * @param string $key1          The key which should precede $key2 in $source_array
   * @param string $key2          The key which should precede $key2 in $source_array
   * @param array $source_array   The source array
   */
  public function assertKeyPrecedesKey($key1, $key2, $source_array) {
    $key1_position = array_search($key1, array_keys($source_array));
    $key2_position = array_search($key2, array_keys($source_array));
    $this->assertGreaterThan($key2_position, $key1_position);
    $this->assertEquals(1, $key2_position-$key1_position);
  }

  /**
   * @param string $key1          The key which should precede $key2 in $source_array
   * @param string $key2          The key which should precede $key2 in $source_array
   * @param array $source_array   The source array
   */
  public function assertKeyImmediatelyPrecedesKey($key1, $key2, $source_array) {
    $key1_position = array_search($key1, array_keys($source_array));
    $key2_position = array_search($key2, array_keys($source_array));
    $this->assertEquals(1, $key2_position-$key1_position, "Key1: $key1 Key2: $key2 Position1: $key1_position Position2: $key2_position");
  }

  /**
   * @param string $key1          The key which should precede $key2 in $source_array
   * @param string $key2          The key which should precede $key2 in $source_array
   * @param array $source_array   The source array
   */
  public function assertKeyFollowsKey($key1, $key2, $source_array) {
    $key1_position = array_search($key1, array_keys($source_array));
    $key2_position = array_search($key2, array_keys($source_array));
    $this->assertLessThan($key2_position, $key1_position);
    $this->assertEquals(1, $key2_position-$key1_position);
  }

  /**
   * @param string $key1          The key which should precede $key2 in $source_array
   * @param string $key2          The key which should precede $key2 in $source_array
   * @param array $source_array   The source array
   */
  public function assertKeyImmediatelyFollowsKey($key1, $key2, $source_array) {
    $key1_position = array_search($key1, array_keys($source_array));
    $key2_position = array_search($key2, array_keys($source_array));
    $this->assertEquals(1, $key1_position-$key2_position);
    $this->assertEquals(1, $key2_position-$key1_position);
  }

}