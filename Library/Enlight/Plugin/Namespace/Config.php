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
     * @var Enlight_Event_Subscriber
     */
	protected $subscriber;

    /**
     * @param   string $name
     * @param   null|array $options
     */
    public function __construct($name, $options = null)
    {
        if(is_array($name)) {
            $options = $name;
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

        parent::__construct($name);
    }

    /**
     * Loads a plugin in the plugin namespace by name.
     *
     * @throws  Enlight_Exception
     * @param   $name
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function load($name)
    {
        if($this->storage->plugins->$name === null
          || $this->plugins->offsetExists($name)) {
            return parent::load($name);
        }
        $item = $this->storage->plugins->$name;

        /** @var $plugin Enlight_Plugin_Bootstrap_Config */
        $plugin = new $item->class($name, $item->config);
        return parent::registerPlugin($plugin);
    }

    /**
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function write()
    {
        $this->storage->plugins = $this->toArray();
        $this->storage->listeners = $this->Subscriber()->toArray();
        $this->storage->write();
        return $this;
    }

    /**
     * Loads all plugins in the plugin namespace.
     *
     * @return  Enlight_Plugin_Namespace_Config
     */
    public function read()
    {
        if($this->storage->plugins !== null) {
            foreach($this->storage->plugins as $name => $value) {
                $this->load($name);
            }
        }
        return $this;
    }

    /**
     * Returns the application instance.
     *
     * @return  Enlight_Event_Subscriber_Plugin
     */
    public function Subscriber()
    {
        if($this->subscriber === null) {
            $this->subscriber = new Enlight_Event_Subscriber_Plugin($this, $this->storage);
        }
        return $this->subscriber;
    }

    public function toArray()
    {
        $this->read();
        $plugins = array();
        /** @var $plugin Enlight_Plugin_Bootstrap_Config */
        foreach($this->plugins as $name => $plugin) {
            $plugins[$name] =  array(
                'name' => $plugin->getName(),
                'class' => get_class($plugin),
                'config' => $plugin->Config()
            );
        }
        return $plugins;
    }
}