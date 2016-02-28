<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\BuildSteps\generic\Command
 *
 * Processes "[build_step]: command:" instructions from within a job definition.
 */

namespace DrupalCI\Plugin\BuildSteps\generic;

use Docker\API\Model\ExecConfig;
use Docker\API\Model\ExecStartConfig;
use Docker\Manager\ExecManager;
use DrupalCI\Console\Output;
use DrupalCI\Plugin\BuildSteps\BuildStepBase;
use DrupalCI\Plugin\JobTypes\JobInterface;

/**
 * @PluginID("command")
 */
class ContainerCommand extends BuildStepBase {

  /**
   * @var integer
   *
   * Store the exit status for the command.
   */
  protected $exit_code;

  protected function setExitCode($exit_code) {
    $this->exit_code = $exit_code;
  }
  public function getExitCode() {
    return $this->exit_code;
  }

  /**
   * {@inheritdoc}
   */
  public function run(JobInterface $job, $data) {
    // Data format: 'command [arguments]' or array('command [arguments]', 'command [arguments]')
    // $data May be a string if one version required, or array if multiple
    // Normalize data to the array format, if necessary
    $data = is_array($data) ? $data : [$data];
    $docker = $job->getDocker();
    $manager = $docker->getContainerManager();

    if (!empty($data)) {
      // Check that we have a container to execute on
      $configs = $job->getExecContainers();
      foreach ($configs as $type => $containers) {
        foreach ($containers as $container) {
          $id = $container['id'];
          $short_id = substr($id, 0, 8);
          Output::writeLn("<info>Executing on container instance $short_id:</info>");
          foreach ($data as $cmd) {
            Output::writeLn("<fg=magenta>$cmd</fg=magenta>");

            $exec_config = new ExecConfig();
            $exec_config->setTty(FALSE);
            $exec_config->setAttachStderr(TRUE);
            $exec_config->setAttachStdout(TRUE);
            $exec_config->setAttachStdin(FALSE);
            $command = ["/bin/bash", "-c", $cmd];
            $exec_config->setCmd($command);

            $exec_manager = $job->getDocker()->getExecManager();
            $response = $exec_manager->create($id, $exec_config);

            $exec_id = $response->getId();
            Output::writeLn("<info>Command created as exec id " . substr($exec_id, 0, 8) . "</info>");

            $exec_start_config = new ExecStartConfig();
            $exec_start_config->setTty(FALSE);
            $exec_start_config->setDetach(FALSE);

            $stream = $exec_manager->start($exec_id, $exec_start_config, [], ExecManager::FETCH_STREAM);

            $stdoutFull = "";
            $stderrFull = "";
            $stream->onStdout(function ($stdout) use (&$stdoutFull) {
              $stdoutFull .= $stdout;
              Output::write($stdout);
            });
            $stream->onStderr(function ($stderr) use (&$stderrFull) {
              $stderrFull .= $stderr;
              Output::write($stderr);
            });
            $stream->wait();

            $exec_command_exit_code = $exec_manager->find($exec_id)->getExitCode();
            Output::writeLn("Command Exit Code: " . $exec_command_exit_code);

            if ($this->checkCommandStatus($exec_command_exit_code) !==0) {
              $job->error();
              break 3;
            }
          }
        }
      }
      // If no errors encountered, assume passed.
      if ($this->getState() != "Error") {
        $this->update("Completed", "Pass", "Command(s) executed on container.");
      }
    }
    else {
      $this->update("Completed", "Warning", "No command passed to build step for execution");
      return;
    }
  }

  protected function checkCommandStatus($signal) {
    if ($signal !==0) {
      Output::error('Error', "Received a non-zero return code from the last command executed on the container.  (Return status: " . $signal . ")");
      return 1;
    }
    else {
      return 0;
    }
  }
}
