<?php

/**
 * @file
 * Command class for Run.
 */

namespace DrupalCI\Console\Command;

use DrupalCI\Console\Helpers\ConfigHelper;
use DrupalCI\Console\Output;
use DrupalCI\Job\CodeBase\JobCodeBase;
use DrupalCI\Job\Definition\JobDefinition;
use DrupalCI\Job\Results\JobResults;
use DrupalCI\Plugin\PluginManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends DrupalCICommandBase {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('run')
      ->setDescription('Execute a given job run.')
      // Argument may be the job type or a specific job definition file
      ->addArgument('definition', InputArgument::OPTIONAL, 'Job definition.');
  }

  /**
   * {@inheritdoc}
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $arg = $input->getArgument('definition');

    $config_helper = new ConfigHelper();
    $local_overrides = $config_helper->getCurrentConfigSetParsed();

    // Determine the Job Type based on the first argument to the run command
    if ($arg) {
      $job_type = (strtolower(substr(trim($arg), -4)) == ".yml") ? "generic" : trim($arg);
    }
    else {
      // If no argument defined, then check for a default in the local overrides
      $job_type = (!empty($local_overrides['DCI_JobType'])) ? $local_overrides['DCI_JobType'] : 'generic';
    }

    // Load the associated class for this job type
    /** @var PluginManager $job_plugin_manager */
    $job_plugin_manager = $this->container['plugin.manager.factory']->create('JobTypes');

    /** @var $job \DrupalCI\Plugin\JobTypes\JobInterface */
    $job = $job_plugin_manager->getPlugin($job_type, $job_type);

    // Link our $output variable to the job, so that jobs can display their work.
    Output::setOutput($output);

    // Generate a unique job build_id, and store it within the job object
    $job->generateBuildId();

    // Create our job Codebase object and attach it to the job.
    $job_codebase = new JobCodebase();
    $job->setJobCodebase($job_codebase);

    // Create our job Definition object and attach it to the job.
    $job_definition = new JobDefinition();
    $job->setJobDefinition($job_definition);

    // Compile our complete list of DCI_* variables
    $job_definition->compile($job);

    // Setup our project and version metadata
    $job_codebase->setupProject($job_definition);

    // Determine the job definition template to be used
    if ($arg && strtolower(substr(trim($arg), -4)) == ".yml") {
      $template_file = $arg;
    }
    else {
      $template_file = $job->getDefaultDefinitionTemplate($job_type);
    }

    $this->logger->notice("<comment>Using job definition template: <options=bold>$template_file</options=bold></comment>");

    // Load our job template file into the job definition.  If $template_file
    // doesn't exist, this will trigger a FileNotFound or ParseError exception.
    $job_definition->loadTemplateFile($template_file);

    // Process the complete job definition, taking into account DCI_* variable
    // and definition preprocessors, along with job-specific arguments
    $job_definition->preprocess($job);

    // Validate the resulting job definition, to ensure all required parameters
    // are present.
    $result = $job_definition->validate($job);
    if (!$result) {
      // Job definition failed validation.  Error output has already been
      // generated and displayed during execution of the validation method.
      return;
    }

    // Set up the local working directory
    $result = $job_codebase->setupWorkingDirectory($job_definition);
    if ($result === FALSE) {
      // Error encountered while setting up the working directory. Error output
      // has already been generated and displayed during execution of the
      // setupWorkingDirectory method.
      return;
    }

    // Create our job Results object and attach it to the job.
    $job_results = new JobResults($job);
    $job->setJobResults($job_results);

    // The job should now have a fully merged job definition file, including
    // any local or DrupalCI defaults not otherwise defined in the passed job
    // definition
    $definition = $job_definition->getDefinition();

    /** @var PluginManager $build_steps_plugin_manager */
    $build_steps_plugin_manager = $this->container['plugin.manager.factory']->create('BuildSteps');

    // Iterate over the build stages
    foreach ($definition as $build_stage => $steps) {
      // Post-processing needs to happen outside of this loop, in case any one
      // build stage fails and aborts the job.
      if ($build_stage == "postprocess") {
        break;
      }
      if (empty($steps)) {
        $job_results->updateStageStatus($build_stage, 'Skipped');
        continue;
      }
      $job_results->updateStageStatus($build_stage, 'Executing');

      // Iterate over the build steps
      foreach ($steps as $build_step => $data) {
        $job_results->updateStepStatus($build_stage, $build_step, 'Executing');
        // Execute the build step
        /** @var Plugin $plugin */
        $plugin = $build_steps_plugin_manager->getPlugin($build_stage, $build_step);
        $plugin->run($job, $data);
        // Update the job results object with build step result information
        $state = $plugin->getState();
        $result = $plugin->getResult();
        $summary = $plugin->getSummary();
        $job_results->updateStepStatus($build_stage, $build_step, $state, $result, $summary);

        // Build step plugins are responsible for updating their own status.  Failure to do so will result in an error.
        // Check for build errors after build step execution
        if ($state != "Completed") {
          Output::error("Build Error", "Error encountered while executing job build step <options=bold>$build_stage:$build_step</options=bold>");
          $job->getJobResults()->setStateByStage($build_stage, "Error");
          $job->getJobResults()->setResultByStage($build_stage, "Error");
          $job->getJobResults()->setSummaryByStage($build_stage, "Build error encountered while executing job build step <options=bold>$build_stage:$build_step</options=bold>.  Step returned state $state.");
          break 2;
        }
        $status = $job->getJobResults()->getResultByStep($build_stage, $build_step);
        if ($status == 'Error') {
          // Step returned an error.  Halt execution.
          $this->logger->error("Error encountered while executing job build step <options=bold>$build_stage:$build_step</options=bold>");
          $job->getJobResults()->updateStepStatus($build_stage, $build_step, "Error", $status);
          break 2;
        }
        if ($status == 'Fail') {
          // Step returned an failure.  Halt execution.
          $this->logger->error("Build step <options=bold>$build_stage:$build_step</options=bold> FAILED");
          break 2;
        }
        $job_results->updateStepStatus($build_stage, $build_step, 'Completed');
      }
      $job_results->updateStageStatus($build_stage, 'Completed');
      // TODO: Update Stage Result
      // TODO: Update Stage Summary
    }

    // Perform post-processing steps
    if (empty($definition["postprocess"])) {
      $job_results->updateStageStatus("postprocess", 'Skipped');
      return;
    }
    foreach ($definition["postprocess"] as $build_step => $data) {
      // Iterate over build steps
      $job_results->updateStepStatus("postprocess", $build_step, 'Executing');
      // Execute the build step
      $build_steps_plugin_manager->getPlugin("postprocess", $build_step)->run($job, $data);
      // Check for errors / failures after build step execution
      $status = $job_results->getResultByStep("postprocess", $build_step);

      // Check for errors / failures after build step execution
      if ($status == 'Error') {
        // Step returned an error.  Halt execution.
        Output::error("Execution Error", "Error encountered while executing job build step <options=bold>postprocess:$build_step</options=bold>");
        break;
      }
    }
  }
}
