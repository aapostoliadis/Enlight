<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
class Enlight_Plugin_Namespace_Loader extends Enlight_Plugin_Namespace
{
    /**
     * @var array
     */
    protected $prefixPaths = array();

    /**
     * @param   string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct();
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param   $name
     * @return  Enlight_Plugin_Bootstrap
     */
    public function get($name)
    {
        if(!$this->plugins->offsetExists($name)) {
            $this->loadPlugin($name);
        }
        return $this->plugins->offsetGet($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function loadPlugin($name)
    {
        if($this->plugins->offsetExists($name)) {
            return $this;
        }
        foreach ($this->prefixPaths as $path => $prefix) {
            $file = $path . $name . $this->Application()->DS() . 'Bootstrap.php';
            if(!file_exists($file)) {
                continue;
            }
            $class = implode('_', array($prefix, $plugin, 'Bootstrap'));
            Enlight_Application::Instance()->Loader()->loadClass($class, $file);
            $this->plugins[$plugin] = Enlight_Class::Instance($class, array($this, $plugin));
            return $this;
        }
        throw new Enlight_Exception();
    }

    /**
     * @throws  Enlight_Exception
     * @param   string $prefix
     * @param   string $path
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function addPrefixPath($prefix, $path)
    {
        if(!file_exists($path) || !is_dir($path)) {
            throw new Enlight_Exception('Parameter path "'.$path.'" is not a valid directory failure');
        }
        $prefix = trim($prefix, '_');
        $path = realpath($path) . $this->Application()->DS();
        $this->prefixPaths[$path] = $prefix;
        return $this;
    }

    /**
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function loadAll()
    {
        foreach ($this->prefixPaths as $path => $prefix) {
            foreach (new DirectoryIterator($path) as $dir) {
                if(!$dir->isDir() || $dir->isDot()){
                    continue;
                }
                $file = $dir->getPathname() . Enlight_Application::DS() . 'Bootstrap.php';
                if(!file_exists($file)){
                    continue;
                }
                $plugin = $dir->getFilename();
                $this->loadPlugin($plugin);
            }
        }
        return $this;
    }

    /**
     * @return  array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function resetPlugins()
    {
        $this->list = array();
        return $this;
    }

    /**
     * @return  int
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayObject($this->list);
    }

    /**
     * @param   string $name
     * @param   null|array $args
     * @return  Enlight_Plugin_PluginBootstrap
     */
    public function __call ($name, $args=null)
    {
        return $this->getPlugin($name);
    }
}