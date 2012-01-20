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
 * The Enlight_Hook_HookArgs are an array of hook arguments which will be passed by the manager to the hook handler.
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
     * @var Enlight_Class class to which the hook is created.
     */
    protected $_class;

    /**
     * @var string method on which the hook is created.
     */
    protected $_method;

    /**
     * @var string name of the class
     */
    protected $_name;

    /**
     * @var mixed return value of the hook arguments
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
     * Standard getter function to return the class property
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_class;
    }

    /**
     * Standard getter function to return the method property
     * @return mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Standard getter function to return the array values of the elements property
     * @return array
     */
    public function getArgs()
    {
        return array_values($this->_elements);
    }

    /**
     * Standard getter function to return the name property
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Standard setter function to set the return property
     * @param $return
     */
    public function setReturn($return)
    {
        $this->_return = $return;
    }

    /**
     * Standard getter function to return the return property
     * @return mixed
     */
    public function getReturn()
    {
        return $this->_return;
    }

    /**
     * Sets the given property to null.
     * @param $key
     */
    public function remove($key)
    {
        $this->set($key, null);
    }

    /**
     * Standard set function to set the value to the given property
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