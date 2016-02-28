<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\environment\WebEnvironment
 *
 * Processes "environment: web:" parameters from within a job definition,
 * ensures appropriate Docker container images exist, and defines the
 * appropriate execution container for communication back to JobBase.
 */

namespace DrupalCI\Plugin\BuildSteps\environment;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("web")
 */
class WebEnvironment extends PhpEnvironment {

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format: '5.5' or array('5.4', '5.5')
    // May also include minor version, eg. '5.5.9'
    // $data May be a string if one version required, or array if multiple
    // Normalize data to the array format, if necessary
    $data = is_array($data) ? $data : [$data];
    Output::writeLn("<info>Parsing required Web container image names ...</info>");
    $containers = $job->getExecContainers();
    $containers['web'] = $this->buildImageNames($data, $job);
    $valid = $this->validateImageNames($containers['web'], $job);
    if (!empty($valid)) {
      $job->setExecContainers($containers);
      // Actual creation and configuration of the executable containers occurs
      // during the 'getExecContainers()' method call.
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
        $images["web-$version"]['image'] = "drupalci/web-$version";
        Output::writeLn("<comment>Adding image: <options=bold>drupalci/web-$version</options=bold></comment>");
      }
      else {
        // TODO: Error Handling
        // For now, the container name will not be found and things will bail out.
      }
    }
    return $images;
  }

}
