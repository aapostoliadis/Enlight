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
 * The Enlight_Plugin_PluginCollection is an array for each registered plugin.
 *
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Plugin_PluginCollection extends Enlight_Class implements IteratorAggregate
{
    /**
     * Property which contains all registered plugins.
     * @var ArrayObject
     */
    protected $plugins;

    /**
     * The Enlight_Plugin_PluginCollection initial the internal plugin list.
     */
    public function __construct()
    {
        $this->plugins = new ArrayObject();
        parent::__construct();
    }

    /**
     * Returns the application instance.
     *
     * @return Enlight_Application
     */
    abstract public function Application();

    /**
     * Registers the given plugin bootstrap. The Enlight_Plugin_PluginCollection instance will be
     * set into the plugin by using the Enlight_Plugin_Bootstrap::setCollection() method.
     * The name of the plugin will be used as array key.
     *
     * @param Enlight_Plugin_Bootstrap $plugin
     * @return Enlight_Plugin_PluginManager
     */
    public function registerPlugin(Enlight_Plugin_Bootstrap $plugin)
    {
        $plugin->setCollection($this);
        $this->plugins[$plugin->getName()] = $plugin;
        return $this;
    }

    /**
     * Getter method for the plugin list.
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return $this->plugins;
    }

    /**
     * Returns a plugin by name. If the plugin isn't registered, the Enlight_Plugin_PluginCollection
     * will load it automatically.
     *
     * @param   $name
     * @return  Enlight_Plugin_Namespace|Enlight_Plugin_Bootstrap
     */
    public function get($name)
    {
        if (!$this->plugins->offsetExists($name)) {
            $this->load($name);
        }
        return $this->plugins->offsetGet($name);
    }

    /**
     *
     * @throws  Enlight_Exception
     * @param   $name
     * @return  Enlight_Plugin_PluginCollection
     */
    public function load($name)
    {
        if (!$this->plugins->offsetExists($name)) {
            throw new Enlight_Exception('Plugin "' . $name . '" not found failure');
        }
        return $this;
    }

    /**
     * @param   string     $name
     * @param   array|null $args
     * @return  Enlight_Plugin_Namespace|Enlight_Plugin_Bootstrap
     */
    public function __call($name, $args = null)
    {
        return $this->get($name);
    }

    /**
     * @return Enlight_Plugin_PluginManager
     */
    public function reset()
    {
        $this->plugins->exchangeArray(array());
        return $this;
    }
}