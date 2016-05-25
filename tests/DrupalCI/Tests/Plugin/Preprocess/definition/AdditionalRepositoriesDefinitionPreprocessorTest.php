<?php
use DrupalCI\Plugin\Preprocess\definition\AdditionalRepositories;
use DrupalCI\Tests\Plugin\Preprocess\definition\DefinitionPreprocessorTestBase;

/**
 * @file
 * contains \DrupalCI\Tests\Plugin\Preprocess\definition\AdditionalRepositoriesDefinitionPreprocessorTest
 *
 * @group Plugin
 * @group DefinitionPreprocessor
 */

class AdditionalRepositoriesDefinitionPreprocessorTest extends DefinitionPreprocessorTestBase
{
  /**
   * @param string $input_value       The value of the DCI_AdditionalRepositories variable
   * @param array $expected_result   An expected array subset for the $definition['setup']['checkout'] elements
   * @param array $expected_result   The expected result for the $definition['setup']['checkout'] elements
   *
   * @dataProvider provideAdditionalRepositoriesValidEntriesDefinitionPreprocessorInputDefinitions
   */
  public function testAdditionalRepositoriesDefinitionPreprocessor($input_value, $expected_result1, $expected_result2) {

    $definition = $this->getDefinitionTemplate();
    $dci_variables = $this->getDCIVariables();
    $plugin = new AdditionalRepositories();
    $plugin->process($definition, $input_value, $dci_variables);
    $this->assertEquals($expected_result1, $definition['setup']['checkout'][1]);
    if (!empty($expected_result2)) {
      $this->assertEquals($expected_result2, $definition['setup']['checkout'][2]);
    }

  }

  public function provideAdditionalRepositoriesValidEntriesDefinitionPreprocessorInputDefinitions() {
    $repo1 = [
      'protocol' => "git",
      'repo' => "http://git.drupal.org/project/token.git",
      'branch' => "8.x-1.x",
      'checkout_dir' => "sites/all/modules/token",
    ];

    $repo2 = [
      'protocol' => 'git',
      'repo' => 'http://git.drupal.org/project/pathauto.git',
      'branch' => '8.x-1.x',
      'checkout_dir' => 'sites/all/modules/pathauto',
    ];
    $depth_value = ['depth' => "1"];
    $repo1depth = $repo1 + $depth_value;
    $repo2depth = $repo2 + $depth_value;


    return [
      // Single repo
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token', $repo1, []],
      // Single repo with checkout depth
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token,1', $repo1depth, []],
      // Single repo, trailing semicolon
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token;', $repo1, []],
      // Multiple repos, first with checkout depth
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token,1;git,http://git.drupal.org/project/pathauto.git,8.x-1.x,sites/all/modules/pathauto', $repo1depth, $repo2],
      // Multiple repos, second with checkout depth
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token;git,http://git.drupal.org/project/pathauto.git,8.x-1.x,sites/all/modules/pathauto,1', $repo1, $repo2depth],
      // Multiple repos, both with checkout depth
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token,1;git,http://git.drupal.org/project/pathauto.git,8.x-1.x,sites/all/modules/pathauto,1', $repo1depth, $repo2depth],
      // Multiple repos, trailing semicolon
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token;git,http://git.drupal.org/project/pathauto.git,8.x-1.x,sites/all/modules/pathauto;', $repo1, $repo2],
      // Multiple repos with checkout depth and trailing semicolon
      ['git,http://git.drupal.org/project/token.git,8.x-1.x,sites/all/modules/token;git,http://git.drupal.org/project/pathauto.git,8.x-1.x,sites/all/modules/pathauto,1;', $repo1, $repo2depth],
    ];
  }
}