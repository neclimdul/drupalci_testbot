<?php
/**
 * @file
 * Contains \DrupalCI\Plugin\PluginManager.
 */

namespace DrupalCI\Plugin;

use Drupal\Component\Annotation\Plugin\Discovery\AnnotatedClassDiscovery;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use DrupalCI\Injectable;
use DrupalCI\InjectableTrait;
use Pimple\Container;

class PluginManager implements Injectable {

  use InjectableTrait;

  /**
   * @var array
   */
  protected $plugins;

  /**
   * @var string
   */
  protected $superPluginType;

  /**
   * @var array
   */
  protected $pluginDefinitions;

  public function __construct($super_plugin_type, Container $container) {
    $this->superPluginType = $super_plugin_type;
    $this->container = $container;
  }

  /**
   * Discovers the list of available plugins.
   */
  protected function discoverPlugins() {
    $dir = __DIR__ . "/../../DrupalCI/Plugin/$this->superPluginType";
    $plugin_definitions = [];
    foreach (new \DirectoryIterator($dir) as $file) {
      if ($file->isDir() && !$file->isDot()) {
        $plugin_type = $file->getFilename();
        $plugin_namespaces = ["DrupalCI\\Plugin\\$this->superPluginType\\$plugin_type" => ["$dir/$plugin_type"]];
        $discovery  = new AnnotatedClassDiscovery($plugin_namespaces, 'Drupal\Component\Annotation\PluginID');
        $plugin_definitions[$plugin_type] = $discovery->getDefinitions();
      }
    }
    return $plugin_definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPlugin($type, $plugin_id) {
    if (!isset($this->pluginDefinitions)) {
      $this->pluginDefinitions = $this->discoverPlugins();
    }
    return (isset($this->pluginDefinitions[$type][$plugin_id]) || isset($this->pluginDefinitions['generic'][$plugin_id]));
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin($type, $plugin_id, $configuration = []) {
    if (!isset($this->plugins[$type][$plugin_id])) {
      if (!$this->hasPlugin($type, $plugin_id)) {
        throw new PluginNotFoundException("Plugin type $type plugin id $plugin_id not found.");
      }
      $plugin_definition = isset($this->pluginDefinitions[$type][$plugin_id]) ?
        $this->pluginDefinitions[$type][$plugin_id] :
        $this->pluginDefinitions['generic'][$plugin_id];
      $plugin = new $plugin_definition['class']($configuration, $plugin_id, $plugin_definition);
      if ($plugin instanceof Injectable) {
        $plugin->setContainer($this->container);
      }
      $this->plugins[$type][$plugin_id] = $plugin;
    }
    return $this->plugins[$type][$plugin_id];
  }
}
