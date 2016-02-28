<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\environment\EnvironmentBase
 */

namespace DrupalCI\Plugin\BuildSteps\environment;

use DrupalCI\Console\Output;
use DrupalCI\Plugin\BuildSteps\BuildStepBase;
use DrupalCI\Plugin\JobTypes\JobInterface;
use DrupalCI\Plugin\PluginBase;
use Http\Client\Plugin\Exception\ClientErrorException;

/**
 * Base class for 'environment' plugins.
 */
abstract class EnvironmentBase extends BuildStepBase {

  public function validateImageNames($containers, JobInterface $job) {
    // Verify that the appropriate container images exist
    Output::writeLn("<comment>Validating container images exist</comment>");
    $docker = $job->getDocker();
    $manager = $docker->getImageManager();
    foreach ($containers as $key => $image_name) {
      $container_string = explode(':', $image_name['image']);
      $name = $container_string[0];
      $tag = empty($container_string[1]) ? 'latest' : $container_string[1];

      try {
        $image = $manager->find($image_name['image']);
      }
      catch (ClientErrorException $e) {
        Output::error("Missing Image", "Required container image <options=bold>'$name:$tag'</options=bold> not found.");
        $job->error();
        return FALSE;
      }
      $id = substr($image->getID(), 0, 8);
      Output::writeLn("<comment>Found image <options=bold>$name:$tag</options=bold> with ID <options=bold>$id</options=bold></comment>");
    }
    return TRUE;
  }
}
