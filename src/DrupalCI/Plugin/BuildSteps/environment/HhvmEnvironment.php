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
 * @PluginID("hhvm")
 */
class HhvmEnvironment extends EnvironmentBase {

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
    $containers['hhvm'] = $this->buildImageNames();
    $valid = $this->validateImageNames($containers['hhvm'], $job);
    if (!empty($valid)) {
      $job->setExecContainers($containers);
      // Actual creation and configuration of the executable containers will occur in the 'execute' plugin.
    }
  }

  protected function buildImageNames() {
    $images = [];
    $images['hhvm-3.7.0']['image'] = "drupalci/hhvm-base";
    Output::writeLn("<comment>Adding image: <options=bold>drupalci/hhvm-base</options=bold></comment>");

    return $images;
  }
}
