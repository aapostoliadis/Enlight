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
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Event_Handler_Plugin extends Enlight_Event_Handler
{
    /**
     * @var string
     */
    protected $listener;

    /**
     * @var Enlight_Plugin_Namespace
     */
    protected $namespace;

    /**
     * @var Enlight_Plugin_Bootstrap|string
     */
    protected $plugin;

    /**
     * @throws  Enlight_Event_Exception
     * @param   string                   $event
     * @param   Enlight_Plugin_Namespace $namespace
     * @param   Enlight_Plugin_Bootstrap $plugin
     * @param   string                   $listener
     * @param   integer                  $position
     */
    public function __construct($event, $namespace = null, $plugin = null, $listener = null, $position = null)
    {
        if ($namespace !== null) {
            $this->setNamespace($namespace);
        }
        if ($plugin !== null) {
            $this->setPlugin($plugin);
        }
        if ($listener !== null) {
            $this->setListener($listener);
        }
        parent::__construct($event);
        $this->setPosition($position);
    }

    /**
     * @param   $plugin Enlight_Plugin_Bootstrap|string
     * @return  Enlight_Event_Handler_Plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @return  Enlight_Plugin_Bootstrap
     */
    public function Plugin()
    {
        if (!$this->plugin instanceof Enlight_Plugin_Bootstrap) {
            $this->plugin = $this->namespace->get($this->plugin);
        }
        return $this->plugin;
    }

    /**
     * @param   string $listener
     * @return  Enlight_Event_Handler_Plugin
     */
    public function setListener($listener)
    {
        $this->listener = $listener;
        return $this;
    }

    /**
     * @param   Enlight_Plugin_Namespace $namespace
     * @return  Enlight_Event_Handler_Plugin
     */
    public function setNamespace(Enlight_Plugin_Namespace $namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return  mixed
     */
    public function execute(Enlight_Event_EventArgs $args)
    {
        return $this->Plugin()->{$this->listener}($args);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->name,
            'position' => $this->position,
            'plugin' => $this->Plugin()->getName(),
            'listener' => $this->listener
        );
    }
}