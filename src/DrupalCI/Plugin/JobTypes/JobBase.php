<?php
/**
 * @file
 * Base Job class for DrupalCI.
 */

namespace DrupalCI\Plugin\JobTypes;

use Drupal\Component\Annotation\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use DrupalCI\Console\Output;
use DrupalCIResultsApi\Api;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use DrupalCI\Console\Jobs\ContainerBase;
use Docker\Docker;
use Docker\Http\DockerClient as Client;
use Symfony\Component\Yaml\Yaml;
use Docker\Container;

class JobBase extends ContainerBase implements JobInterface {

  // Defines the job type
  public $jobtype = 'base';

  // Defines a unique build ID
  public $buildId;

  /**
   * @param mixed $buildId
   */
  public function setBuildId($buildId)
  {
    $this->buildId = $buildId;
  }

  /**
   * @return mixed
   */
  public function getBuildId()
  {
    return $this->buildId;
  }

  // Defines the job definition file
  protected $jobDefinitionFile;

  // Defines argument variable names which are valid for this job type
  public $availableArguments = array();

  // Defines platform defaults which apply for all jobs.  (Can still be overridden by per-job defaults)
  public $platformDefaults = array(
    "DCI_CodeBase" => "./",
    // DCI_CheckoutDir defaults to a random directory in the system temp directory.
  );

  // Defines the default arguments which are valid for this job type
  public $defaultArguments = array();

  // Defines the required arguments which are necessary for this job type
  // Format:  array('ENV_VARIABLE_NAME' => 'CONFIG_FILE_LOCATION'), where
  // CONFIG_FILE_LOCATION is a colon-separated nested location for the
  // equivalent var in a job definition file.
  public $requiredArguments = array(
    // eg:   'DCI_DBVersion' => 'environment:db'
  );

  // Placeholder which holds the parsed job definition file for this job
  public $jobDefinition = NULL;

  // Error status
  public $errorStatus = 0;

  // Default working directory
  public $workingDirectory = "./";

  /**
   * @var array
   */
  protected $pluginDefinitions;

  /**
   * @var array
   */
  protected $plugins;

  // Holds the name and Docker IDs of our service containers.
  public $serviceContainers;

  // Holds the name and Docker IDs of our executable containers.
  public $executableContainers = [];

  // Holds our Docker container manager
  protected $docker;

  // Holds build variables which need to be persisted between build steps
  public $buildVars = array();

  // Holds our DrupalCIResultsAPI API
  protected $resultsAPI = NULL;

  /**
   * @param API
   */
  public function setResultsAPI($resultsAPI)
  {
    $this->resultsAPI = $resultsAPI;
  }

  /**
   * @return API
   */
  public function getResultsAPI()
  {
    if (is_null($this->resultsAPI)) {
      $api = new API();
      $this->setResultsAPI($api);
    }
    return $this->resultsAPI;
  }

  public function configureResultsAPI($instance) {
    $api = $this->getResultsAPI();
    if (!empty($instance['config'])) {
      $config = $this->loadAPIConfig($instance['config']);
    }
    else {
      $config['results'] = $instance;
    }
    $api->setUrl($config['results']['host']);
    if (!empty($config['results']['username'])) {
      // Handle case where no password is provided
      if (empty($config['results']['password'])) {
        $config['results']['password'] = '';
      }
      // Set authorization parameters on the API object
      $api->setAuth($config['results']['username'], $config['results']['password']);
    }
    $this->setResultsAPI($api);
  }

  protected function loadAPIConfig($source) {
    $config = array();
    $source = realpath($source);
    if ($content = file_get_contents($source)) {
      $parsed = Yaml::parse($content);
      $config['results']['host'] = $parsed['results']['host'];
      $config['results']['username'] = $parsed['results']['username'];
      $config['results']['password'] = $parsed['results']['password'];
    }
    return $config;
  }

  // Stores a drupalci_results server node ID for this job
  public $resultsServerID;

  public function setResultsServerID($resultsServerID)
  {
    $this->resultsServerID = $resultsServerID;
  }

  /**
   * @return mixed
   */
  public function getResultsServerID()
  {
    return $this->resultsServerID;
  }

  /**
   * Stores the calling command's output buffer
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  public $output;

  public function getBuildVars() {
    return $this->buildVars;
  }

  // Sets the build variables for this job
  public function setBuildVars(array $build_vars) {
    $this->buildVars = $build_vars;
  }

  // Retrieves a single build variable for this job
  public function getBuildvar($build_var) {
    return isset($this->buildVars[$build_var]) ? $this->buildVars[$build_var] : NULL;
  }

  // Sets a single build variable for this job
  public function setBuildVar($build_var, $value) {
    $this->buildVars[$build_var] = $value;
  }

  public function getRequiredArguments() {
    return $this->requiredArguments;
  }

  public function setOutput(OutputInterface $output) {
    $this->output = $output;
  }

  public function getOutput() {
    return $this->output;
  }

  public function getDefinition() {
    return $this->jobDefinition;
  }

  public function setDefinition(array $job_definition) {
    $this->jobDefinition = $job_definition;
  }

  public function getDefinitionFile() {
    return $this->jobDefinitionFile;
  }

  public function setDefinitionFile($filename) {
    $this->jobDefinitionFile = $filename;
  }
  public function getDefaultArguments() {
    return $this->defaultArguments;
  }

  public function getPlatformDefaults() {
    return $this->platformDefaults;
  }

  public function getServiceContainers() {
    return $this->serviceContainers;
  }

  public function setServiceContainers(array $service_containers) {
    $this->serviceContainers = $service_containers;
  }

  public function getWorkingDir() {
    return $this->workingDirectory;
  }

  public function setWorkingDir($working_directory) {
    $this->workingDirectory = $working_directory;
  }

  public function errorOutput($type = 'Error', $message = 'DrupalCI has encountered an error.') {
    Output::error($type, $message);
    $this->errorStatus = -1;
  }

  public function shellCommand($cmd) {
    $process = new Process($cmd);
    $process->setTimeout(3600*6);
    $process->setIdleTimeout(3600);
    $process->run(function ($type, $buffer) {
        Output::writeln($buffer);
    });
   }

  protected function discoverPlugins() {
    $dir = __DIR__ . '/../../../DrupalCI/Plugin';
    $plugin_definitions = [];
    foreach (new \DirectoryIterator($dir) as $file) {
      if ($file->isDir() && !$file->isDot()) {
        $plugin_type = $file->getFilename();
        $plugin_namespaces = ["DrupalCI\\Plugin\\$plugin_type" => ["$dir/$plugin_type"]];
        $discovery  = new AnnotatedClassDiscovery($plugin_namespaces, 'Drupal\Component\Annotation\PluginID');
        $plugin_definitions[$plugin_type] = $discovery->getDefinitions();
      }
    }
    return $plugin_definitions;
  }

  /**
   * @return \DrupalCI\Plugin\PluginBase
   */
  protected function getPlugin($type, $plugin_id, $configuration = []) {
    if (!isset($this->pluginDefinitions)) {
      $this->pluginDefinitions = $this->discoverPlugins();
    }
    if (!isset($this->plugins[$type][$plugin_id])) {
      if (isset($this->pluginDefinitions[$type][$plugin_id])) {
        $plugin_definition = $this->pluginDefinitions[$type][$plugin_id];
      }
      elseif (isset($this->pluginDefinitions['generic'][$plugin_id])) {
        $plugin_definition = $this->pluginDefinitions['generic'][$plugin_id];
      }
      else {
        throw new PluginNotFoundException("Plugin type $type plugin id $plugin_id not found.");
      }
      $this->plugins[$type][$plugin_id] = new $plugin_definition['class']($configuration, $plugin_id, $plugin_definition);
    }
    return $this->plugins[$type][$plugin_id];
  }

  public function getDocker()
  {
    $client = Client::createWithEnv();
    if (null === $this->docker) {
      $this->docker = new Docker($client);
    }
    return $this->docker;
  }

  public function getExecContainers() {
    $configs = $this->executableContainers;
    foreach ($configs as $type => $containers) {
      foreach ($containers as $key => $container) {
        // Check if container is created.  If not, create it
        if (empty($container['created'])) {
          // TODO: This may be causing duplicate containers to be created
          // due to a race condition during short-running exec calls.
          $this->startContainer($container);
          $this->executableContainers[$type][$key] = $container;
        }
      }
    }
    return $this->executableContainers;
  }

  public function setExecContainers(array $containers) {
    $this->executableContainers = $containers;
  }

  public function startContainer(&$container) {
    $docker = $this->getDocker();
    $manager = $docker->getContainerManager();
    // Get container configuration, which defines parameters such as exposed ports, etc.
    $configs = $this->getContainerConfiguration($container['image']);
    $config = $configs[$container['image']];
    // TODO: Allow classes to modify the default configuration before processing
    // Add service container links
    $this->createContainerLinks($config);
    // Add volumes
    $this->createContainerVolumes($config);
    // Set a default CMD in case the container config does not set one.
    if (empty($config['Cmd'])) {
      $this->setDefaultCommand($config);
    }

    $instance = new Container($config);
    $manager->create($instance);

    $manager->run($instance, function($output, $type) {
      fputs($type === 1 ? STDOUT : STDERR, $output);
    }, [], true);

    $container['id'] = $instance->getID();
    $container['name'] = $instance->getName();
    $container['created'] = TRUE;
    $short_id = substr($container['id'], 0, 8);
    Output::writeln("<comment>Container <options=bold>${container['name']}</options=bold> created from image <options=bold>${container['image']}</options=bold> with ID <options=bold>$short_id</options=bold></comment>");
  }

  protected function setDefaultCommand(&$config) {
    $config['Cmd'] = ['/bin/bash', '-c', '/daemon.sh'];
  }

  protected function createContainerLinks(&$config) {
    $links = array();
    if (empty($this->serviceContainers)) {
      return;
    }
    $targets = $this->serviceContainers;
    foreach ($targets as $type => $containers) {
      foreach ($containers as $key => $container) {
        $config['HostConfig']['Links'][] = "${container['name']}:${container['name']}";
      }
    }
  }

  protected function createContainerVolumes(&$config) {
    $volumes = array();
    // Map working directory
    $working = $this->workingDirectory;
    $mount_point = (empty($config['Mountpoint'])) ? "/data" : $config['Mountpoint'];
    $config['HostConfig']['Binds'][] = "$working:$mount_point";
  }

  public function getContainerConfiguration($image = NULL) {
    $path = __DIR__ . '/../../Containers';
    // RecursiveDirectoryIterator recurses into directories and returns an
    // iterator for each directory. RecursiveIteratorIterator then iterates over
    // each of the directory iterators, which consecutively return the files in
    // each directory.
    $directory = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
    $configs = [];
    foreach ($directory as $file) {
      if (!$file->isDir() && $file->isReadable() && $file->getExtension() === 'yml') {
        $image_name = 'drupalci/' . $file->getBasename('.yml');
        if (!empty($image) && $image_name != $image) {
          continue;
        }
        // Get the default configuration.
        $container_config = Yaml::parse(file_get_contents($file->getPathname()));
        $configs[$image_name] = $container_config;
      }
    }
    return $configs;
  }

  public function startServiceContainerDaemons($container_type) {
    $needs_sleep = FALSE;
    $docker = $this->getDocker();
    $manager = $docker->getContainerManager();
    $instances = array();
    foreach ($manager->findAll() as $running) {
      $repo = $running->getImage()->getRepository();
      $id = substr($running->getID(), 0, 8);
      $instances[$repo] = $id;
    };
    foreach ($this->serviceContainers[$container_type] as $key => $image) {
      if (in_array($image['image'], array_keys($instances))) {
        // TODO: Determine service container ports, id, etc, and save it to the job.
        Output::writeln("<comment>Found existing <options=bold>${image['image']}</options=bold> service container instance.</comment>");
        // TODO: Load up container parameters
        $container = $manager->find($instances[$image['image']]);
        $container_id = $container->getID();
        $container_name = $container->getName();
        $this->serviceContainers[$container_type][$key]['id'] = $container_id;
        $this->serviceContainers[$container_type][$key]['name'] = $container_name;
        continue;
      }
      // Container not running, so we'll need to create it.
      Output::writeln("<comment>No active <options=bold>${image['image']}</options=bold> service container instances found. Generating new service container.</comment>");

      // Get container configuration, which defines parameters such as exposed ports, etc.
      $configs = $this->getContainerConfiguration($image['image']);
      $config = $configs[$image['image']];
      // TODO: Allow classes to modify the default configuration before processing
      // Instantiate container
      $container = new Container($config);
      if (!empty($config['name'])) {
        $container->setName($config['name']);
      }
      // Create the docker container instance, running as a daemon.
      // TODO: Ensure there are no stopped containers with the same name (currently throws fatal)
      $manager->run($container, function($output, $type) {
        fputs($type === 1 ? STDOUT : STDERR, $output);
      }, [], true);
      $container_id = $container->getID();
      $container_name = $container->getName();
      $this->serviceContainers[$container_type][$key]['id'] = $container_id;
      $this->serviceContainers[$container_type][$key]['name'] = $container_name;
      $short_id = substr($container_id, 0, 8);
      Output::writeln("<comment>Created new <options=bold>${image['image']}</options=bold> container instance with ID <options=bold>$short_id</options=bold></comment>");
      $needs_sleep = TRUE;
    }
    if ($needs_sleep) {
      Output::writeln("Sleeping 10 seconds to allow services to start.");
      sleep(10);
    }
  }

  public function getErrorState() {
    return $this->errorStatus;
  }


}
