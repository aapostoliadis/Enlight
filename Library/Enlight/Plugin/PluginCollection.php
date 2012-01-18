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
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Plugin_PluginCollection extends Enlight_Class implements IteratorAggregate
{
    /**
     * @var ArrayObject
     */
    protected $plugins;

    /**
     *
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
     * Registers a plugin in the collection.
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
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return $this->plugins;
    }

    /**
     * Returns a plugin by name.
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