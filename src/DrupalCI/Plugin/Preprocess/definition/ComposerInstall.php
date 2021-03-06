<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\Preprocess\definition\ComposerInstall
 */

namespace DrupalCI\Plugin\Preprocess\definition;

/**
 * @PluginID("composerinstall")
 *
 * PreProcesses DCI_ComposerInstall variables, updating the job definition with
 * a install:composer:install section.  To use set DCI_ComposerInstall=true.
 */

class ComposerInstall {

  /**
   * {@inheritdoc}
   */
  public function process(array &$definition, $value, $dci_variables) {
    // Presence of the DCI_ComposerInstall variable infers we want to run it.
    if ($value == FALSE) {
      return;
    }

    if (empty($definition['pre-install'])) {
      // Insert the pre-install step at the appropriate spot in the definition.
      // If ['install'] exists, put it immediately before that key.  If not,
      // put it before the ['execute'] key.
      $new_array = [];
      $search_key = (!empty($definition['install'])) ? 'install' : 'execute';
      foreach ($definition as $key => $details) {
        if ($key == $search_key) {
          $new_array['pre-install'] = [];
        }
        $new_array[$key] = $details;
      }
      $definition = $new_array;
    }

    if (empty($definition['pre-install']['composer'])) {
      $definition['pre-install']['composer'] = [];
    }

    $definition['pre-install']['composer'][] = 'install --working-dir /var/www/html/core --prefer-dist';
  }
}
