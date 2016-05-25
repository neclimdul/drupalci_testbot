<?php
use DrupalCI\Plugin\Preprocess\definition\Fetch;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\FetchDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class FetchDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  /**
   * @param string $fetch_value      The value passed into the patch command
   * @param array  $expected_result   Expected Result
   *
   * @dataProvider provideFetchDefinitionPreprocessorInputDefinitions
   */
  public function testFetchDefinitionPreprocessor($patch_value, $expected_result) {
    // Adds $definition['setup']['fetch'] = [ ... fetches ...] section to the definition array.
    // Each element contains the keys 'url' and 'fetch_directory'
    $definition = $this->getDefinitionTemplate();
    $plugin = new Fetch();
    $plugin->process($definition, $patch_value, []);
    $this->assertEquals($expected_result, $definition['setup']['fetch']);

  }

  public function provideFetchDefinitionPreprocessorInputDefinitions() {
    return [
      // Test single fetch with no directory specified
      ['http://example.com/file1.patch', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => '.']]],
      // Test single fetch with directory specified
      ['http://example.com/file1.patch,dir1', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => 'dir1']]],
      // Test multiple fetches with no directory specified
      ['http://example.com/file1.patch;http://example.com/file2.patch', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => '.'],['url' => 'http://example.com/file2.patch', 'fetch_directory' => '.']]],
      // Test multiple fetches with some directories specified
      ['http://example.com/file1.patch,dir1;http://example.com/file2.patch', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => 'dir1'],['url' => 'http://example.com/file2.patch', 'fetch_directory' => '.']]],
      ['http://example.com/file1.patch;http://example.com/file2.patch,dir2', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => '.'],['url' => 'http://example.com/file2.patch', 'fetch_directory' => 'dir2']]],
      // Test multiple fetches with all directories specified
      ['http://example.com/file1.patch,dir1;http://example.com/file2.patch,dir2', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => 'dir1'],['url' => 'http://example.com/file2.patch', 'fetch_directory' => 'dir2']]],
      // Test single fetch with trailing comma
      ['http://example.com/file1.patch,', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => '.']]],
      // Test single fetch with trailing semicolon
      ['http://example.com/file1.patch;', [['url' => 'http://example.com/file1.patch', 'fetch_directory' => '.']]],
    ];
  }

}