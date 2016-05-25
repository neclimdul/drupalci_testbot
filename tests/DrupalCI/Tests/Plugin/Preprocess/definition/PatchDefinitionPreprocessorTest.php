<?php
use DrupalCI\Plugin\Preprocess\definition\Patch;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\PatchDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class PatchDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  /**
   * @param string $patch_value      The value passed into the patch command
   * @param array  $expected_result   Expected Result
   *
   * @dataProvider providePatchDefinitionPreprocessorInputDefinitions
   */
  public function testPatchDefinitionPreprocessor($patch_value, $expected_result) {
    // Adds $definition['setup']['patch'] = [ ... patches ...] section to the definition array.
    // Each element contains the keys 'patch_file' and 'patch_directory'
    $definition = $this->getDefinitionTemplate();
    $plugin = new Patch();
    $plugin->process($definition, $patch_value, []);
    $this->assertEquals($expected_result, $definition['setup']['patch']);
  }

  public function providePatchDefinitionPreprocessorInputDefinitions() {
    return [
      // Test single patch with no directory specified
      ['file1.patch', [['patch_file' => 'file1.patch', 'patch_dir' => '.']]],
      // Test single patch with directory specified
      ['file1.patch,dir1', [['patch_file' => 'file1.patch', 'patch_dir' => 'dir1']]],
      // Test multiple patches with no directory specified
      ['file1.patch;file2.patch', [['patch_file' => 'file1.patch', 'patch_dir' => '.'],['patch_file' => 'file2.patch', 'patch_dir' => '.']]],
      // Test multiple patches with some directories specified
      ['file1.patch,dir1;file2.patch', [['patch_file' => 'file1.patch', 'patch_dir' => 'dir1'],['patch_file' => 'file2.patch', 'patch_dir' => '.']]],
      ['file1.patch;file2.patch,dir2', [['patch_file' => 'file1.patch', 'patch_dir' => '.'],['patch_file' => 'file2.patch', 'patch_dir' => 'dir2']]],
      // Test multiple patches with all directories specified
      ['file1.patch,dir1;file2.patch,dir2', [['patch_file' => 'file1.patch', 'patch_dir' => 'dir1'],['patch_file' => 'file2.patch', 'patch_dir' => 'dir2']]],
      // Test single patch with trailing comma
      ['file1.patch,', [['patch_file' => 'file1.patch', 'patch_dir' => '.']]],
      // Test single patch with trailing semicolon
      ['file1.patch;', [['patch_file' => 'file1.patch', 'patch_dir' => '.']]],
    ];
  }

}