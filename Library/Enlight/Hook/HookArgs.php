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
 * Hook arguments which will be passed to the hook listener.
 *
 * The Enlight_Hook_HookArgs is an array of hook arguments which are passed by the manager to the hook handler.
 * It contains all data about the hook handler (class name, method name, target function, return value)
 *
 * @category   Enlight
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Hook_HookArgs extends Enlight_Collection_ArrayCollection
{
    /**
     * @var Enlight_Class Class to which the hook is created.
     */
    protected $_class;

    /**
     * @var string Method on which the hook is created.
     */
    protected $_method;

    /**
     * @var string Name of the class
     */
    protected $_name;

    /**
     * @var mixed return value of the hook arguments.
     * You can access the return value by the $args->getReturn() method
     * The return value can be overwritten by the $args->setReturn()
     */
    protected $_return;

    /**
     * Class constructor requires the class and method of the hook.
     *
     * @param            $class
     * @param            $method
     * @param array|null $args
     */
    public function __construct($class, $method, array $args = null)
    {
        $this->_name = get_parent_class($class);
        $this->_class = $class;
        $this->_method = $method;
        parent::__construct($args);
    }

    /**
     * Default getter function to return the class property
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_class;
    }

    /**
     * Default getter function to return the method property
     * @return mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Default getter function to return the array values of the elements property
     * @return array
     */
    public function getArgs()
    {
        return array_values($this->_elements);
    }

    /**
     * Default getter function to return the name property
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Default setter function to set the return property
     * @param $return
     */
    public function setReturn($return)
    {
        $this->_return = $return;
    }

    /**
     * Default getter function to return the return property
     *
     * @return mixed
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Sets the given property to null.
     *
     * @param $key
     */
    public function remove($key)
    {
        $this->set($key, null);
    }

    /**
     * Default set function to set the value to the given property
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        if ($this->containsKey($key)) {
            parent::set($key, $value);
        }
    }
}