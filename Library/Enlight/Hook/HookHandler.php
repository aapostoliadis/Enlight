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
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Hook_HookHandler
{
    /**
     * @var class to which the hook is created.
     */
    protected $class;

    /**
     * @var method on which the hook is created.
     */
    protected $method;

    /**
     * @var
     */
    protected $hook;

    /**
     * @var type of the hook (before, replace or after)
     */
    protected $type;

    /**
     * @var position of the hook. If more than one hook exists on the same class method the hooks are called sequentially by the position.
     */
    protected $position;

    /**
     * @var the plugin which creates the hook object.
     */
    protected $plugin;

    const TypeReplace = 1;
    const TypeBefore = 2;
    const TypeAfter = 3;

    /**
     * Class constructor for a hook. The class, method and  the listener are required.
     *
     * @param      $class
     * @param      $method
     * @param      $listener
     * @param int  $type
     * @param int  $position
     * @param null $plugin
     */
    public function __construct($class, $method, $listener, $type = self::TypeAfter, $position = 0, $plugin = null)
    {
        if (empty($class) || empty($method) || empty($listener)) {
            throw new Enlight_Exception('Some parameters are empty');
        }
        if (!is_callable($listener, true, $listener_name)) {
            throw new Enlight_Exception('Listener "' . $listener_name . '" is not callable');
        }
        $this->class = $class;
        $this->method = $method;
        $this->listener = $listener;
        $this->setType($type);
        $this->setPosition($position);
        $this->setPlugin($plugin);
    }

    /**
     * Standard setter function for the type property. If the given type is null the default type (typeAfter) is set.
     * If the given type isn't one of the supported hook types, an exception is thrown.
     *
     * @param $type
     * @return Enlight_Hook_HookHandler
     * @throws Enlight_Exception
     */
    public function setType($type)
    {
        if ($type === null) {
            $type = self::TypeAfter;
        }
        if (!in_array($type, array(self::TypeReplace, self::TypeBefore, self::TypeAfter))) {
            throw new Enlight_Exception('Hook type is unknown');
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Standard setter method of the position property. If the given position isn't numeric an exception is thrown.
     * @param $position
     * @return Enlight_Hook_HookHandler
     * @throws Enlight_Exception
     */
    public function setPosition($position)
    {
        if (!is_numeric($position)) {
            throw new Enlight_Exception('Position is not numeric');
        }
        $this->position = $position;
        return $this;
    }

    /**
     * Standard setter function of the plugin property
     * @param $plugin
     * @return Enlight_Hook_HookHandler
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * Returns the class and method name, concat with '::'
     * @return string
     */
    public function getName()
    {
        return $this->class . '::' . $this->method;
    }

    /**
     * Standard getter function of the class property.
     * @return class
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Standard getter function of the method property.
     * @return method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Standard getter function of the listener property.
     * @return mixed
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * Standard getter function of the type property.
     * @return type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Standard getter function of the position property.
     * @return position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Standard getter function of the plugin property.
     * @return the
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Executes the listener with the given arguments.
     * @param null $args
     * @return mixed
     */
    public function execute($args = null)
    {
        return call_user_func($this->listener, $args);
    }
}