<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Allows to register multiple plugins from a namespace.
 *
 * The Enlight_Plugin_Namespace_Loader reads all plugins from the namespace and register them over the
 * Enlight_Plugin_Manager.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Plugin_Namespace_Loader extends Enlight_Plugin_Namespace
{
    /**
     * @var array List of all added prefix paths
     */
    protected $prefixPaths = array();

    /**
     * Returns the instance of the passed plugin name.
     *
     * @param   $name
     * @return  Enlight_Plugin_Bootstrap
     */
    public function get($name)
    {
        if (!$this->plugins->offsetExists($name)) {
            $this->load($name);
        }
        return $this->plugins->offsetGet($name);
    }

    /**
     * Adds a prefix path to the plugin namespace. Is used for the auto loading of plugins.
     *
     * @throws  Enlight_Exception
     * @param   string $prefix
     * @param   string $path
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function addPrefixPath($prefix, $path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new Enlight_Exception('Parameter path "' . $path . '" is not a valid directory failure');
        }
        $prefix = trim($prefix, '_');
        $path = realpath($path) . DIRECTORY_SEPARATOR;
        $this->prefixPaths[$path] = $prefix;
        return $this;
    }

    /**
     * Instantiates a plugin from the plugin namespace and add it to the internal plugins array.
     *
     * @param   string      $name
     * @param   string      $prefix
     * @param   string|null $file
     * @return  Enlight_Plugin_Namespace_Loader
     */
    protected function initPlugin($name, $prefix, $file = null)
    {
        $class = implode('_', array($prefix, $name, 'Bootstrap'));
        if (!class_exists($class, false)) {
            Enlight_Application::Instance()->Loader()->loadClass($class, $file);
        }
        $plugin = new $class($name, $this);
        $this->plugins[$name] = $plugin;
        return $this;
    }

    /**
     * Loads a plugin in the plugin namespace by name.
     *
     * @param   $name
     * @return  bool
     */
    public function load($name)
    {
        if ($this->plugins->offsetExists($name)) {
            return $this;
        }
        foreach ($this->prefixPaths as $path => $prefix) {
            $file = $path . $name . $this->Application()->DS() . 'Bootstrap.php';
            if (!file_exists($file)) {
                continue;
            }
            $this->initPlugin($name, $prefix, $file);
            return $this;
        }
        throw new Enlight_Exception('Plugin "' . $name .'" in namespace "' . $this->getName() .'" not found');
    }

    /**
     * Loads all plugins in the plugin namespace. Iterate the prefix paths and looking for bootstrap files.
     *
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function loadAll()
    {
        foreach ($this->prefixPaths as $path => $prefix) {
            foreach (new DirectoryIterator($path) as $dir) {
                if (!$dir->isDir() || $dir->isDot()) {
                    continue;
                }
                $file = $dir->getPathname() . DIRECTORY_SEPARATOR . 'Bootstrap.php';
                if (!file_exists($file)) {
                    continue;
                }
                $name = $dir->getFilename();
                $this->initPlugin($name, $prefix, $file);
            }
        }
        return $this;
    }
}