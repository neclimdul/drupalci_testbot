<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\ComposerInstall
 */

namespace DrupalCI\Plugin\Preprocess\definition;
use DrupalCI\Console\Output;

/**
 * @PluginID("composerinstall")
 *
 * PreProcesses DCI_ComposerInstall variables, updating the job definition with
 * a setup:composer:install section.  To use set DCI_ComposerInstall=true.
 */

class ComposerInstall {

  /**
   * {@inheritdoc}
   */
  public function process(array &$definition, $value, $dci_variables) {
    // TODO: Ensure that $definition['execute'] exists (minimum requirement)
    // TODO: Should composer go into the 'pre-install' step instead?
    // Check to see if Composer Install is set and true
    if (strtolower($value) !== 'true') {
      return;
    }

    if (empty($definition['setup'])) {
      // Insert the setup step at the appropriate spot in the definition.
      // If ['install'] exists, put it immediately before that key.  If not,
      // put it before the ['execute'] key.
      $new_array = [];
      $search_key = (array_key_exists('install', $definition)) ? 'install' : 'execute';

      $length = array_search($search_key, array_keys($definition));
      $definition =
        array_slice($definition, 0, $length, TRUE) +
        ['setup' => []] +
        array_slice($definition, $length, NULL, TRUE);
    }

    if (empty($definition['setup']['composer'])) {
      $definition['setup']['composer'] = [];
    }

    $definition['setup']['composer'][] = "install --prefer-dist --working-dir ";
  }
}
