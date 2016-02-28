<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\PHPVersion
 *
 * PreProcesses DCI_PHPVersion variables, updating the job definition with a pre-install:phpversion: section.
 */

namespace DrupalCI\Plugin\Preprocess\definition;

/**
 * @PluginID("phpversion")
 */
class PHPVersion {

  /**
   * {@inheritdoc}
   *
   * DCI_PHPVersion_Preprocessor
   *
   * Takes a string defining the PHP version to be used on this test, and
   * converts this to a 'setup:command:echo $version > /opt/phpenv/version'
   * command array in order to tell phpenv which minor version to use.
   *
   * Input format: (string) $value = "5.5.9"
   * Desired Result: [ array(
   *     'setup' => array( ...
   *         'command' => array( ..., "echo 5.5.9 > /opt/phpenv/version", ...)
   *     ... )
   */
  public function process(array &$definition, $php_version, $dci_variables) {
    // Only process the variable if a 'minor' version is defined.
    $pattern = "/^(\d+(\.\d+)?(\.\d+)?)/";
    if (!preg_match($pattern, $php_version, $matches)) {
      // Invalid PHP Version passed.
      // TODO: Add Error Handling
      return;
    }
    if (empty($matches[3])) {
      // No minor version specified
      return;
    }

    // Add the 'setphpversion' entry to the pre-install build step section
    if (empty($definition['setup']['command'])) {
      $definition['setup']['command'] = [];
    }

    // Set the value of the set php version command
    $definition['setup']['command'][] = "echo $php_version > /opt/phpenv/version";
  }
}

