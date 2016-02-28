<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\environment\PhpEnvironment
 *
 * Processes "environment: php:" parameters from within a job definition,
 * ensures appropriate Docker container images exist, and defines the
 * appropriate execution container for communication back to JobBase.
 */

namespace DrupalCI\Plugin\BuildSteps\environment;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("php")
 */
class PhpEnvironment extends EnvironmentBase {

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format: '5.5' or array('5.4', '5.5')
    // $data May be a string if one version required, or array if multiple
    // Normalize data to the array format, if necessary
    $data = is_array($data) ? $data : [$data];
    Output::writeLn("<info>Parsing required PHP container image names ...</info>");
    $containers = $job->getExecContainers();
    $containers['php'] = $this->buildImageNames($data, $job);
    $valid = $this->validateImageNames($containers['php'], $job);
    if (!empty($valid)) {
      $job->setExecContainers($containers);
      // Actual creation and configuration of the executable containers occurs
      // in the getExecContainers() method call.
      $this->update("Completed", "Pass", "Executable container names established.");
    }
    else {
      $this->update("Error", "SystemError", "Error encountered while initializing executable container environment.  No valid executable container names found.");
    }
  }

  protected function buildImageNames($data, JobInterface $job) {
    $images = [];
    foreach ($data as $key => $php_version) {
      // Drop minor version if present
      $pattern = "/^(\d+(\.\d+)?)/";
      if (preg_match($pattern, $php_version, $matches)) {
        $version = $matches[0];
        $images["php-$php_version"]['image'] = "drupalci/php-$php_version";
        Output::writeLn("<comment>Adding image: <options=bold>drupalci/php-$php_version</options=bold></comment>");
      }
      else {
        // TODO: Error Handling
        // For now, the container name will not be found and things will bail out.
      }
    }
    return $images;
  }
}
