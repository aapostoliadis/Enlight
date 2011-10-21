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
class Enlight_Plugin_Namespace_Config extends Enlight_Plugin_Namespace
{
    /**
     * @var Enlight_Config
     */
    protected $storage;

    /**
     * @param $name
     * @param null $options
     */
    public function __construct($name, $options = null)
    {
        if(is_array($name)) {
            $name = $options;
        }

        if(is_string($options)) {
            $options = array('storage' => $options);
        }
        if(!isset($options['storage'])) {
            $options['storage'] = $name;
        }
        if(is_string($options['storage'])) {
            $this->storage = new Enlight_Config($options['storage'], array(
                'allowModifications' => true,
                'adapter' => isset($options['storageAdapter']) ? $options['storageAdapter'] : null,
                'section' => isset($options['section']) ? $options['section'] : 'production'
            ));
        } elseif($options['storage'] instanceof Enlight_Config) {
            $this->storage = $options['storage'];
        }
    }

    /**
     * Loads a plugin in the plugin namespace by name.
     *
     * @throws Enlight_Exception
     * @param $name
     * @return Enlight_Plugin_Namespace_Config
     */
    public function load($name)
    {
        if(!isset($this->storage->$name)) {
            return parent::load($name);
        }
        $config = $this->storage->$name;

        /** @var $plugin Enlight_Plugin_Bootstrap_Config */
        $plugin = new $config->class($name, $config);
        $plugin->setNamespace($this);

        $this->plugins[$name] = $plugin;
        return $this;
    }

    /**
     * Loads all plugins in the plugin namespace.
     *
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function loadAll()
    {
        foreach($this->storage as $name => $value) {
            $this->load($name);
        }
        return $this;
    }

    /**
     * @param Enlight_Plugin_Bootstrap $plugin
     * @return Enlight_Plugin_Namespace_Config
     */
    public function registerPlugin(Enlight_Plugin_Bootstrap $plugin)
    {
        $this->storage->set($plugin->getName(), array(
            'name' => $plugin->getName(),
            'class' => get_class($plugin)
        ))->write();
        return parent::registerPlugin($plugin);
    }
}