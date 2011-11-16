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
 * @package    Enlight_Components_Session
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Components_Session
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Session_Namespace extends Zend_Session_Namespace implements Countable, IteratorAggregate, ArrayAccess
{
	public function offsetExists($key)
    {
        return $this->__isset($key);
    }
    public function offsetUnset($key)
    {
        $this->__unset($key);
    }
    public function offsetGet($key)
    {
        return $this->__get($key);
    }
    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }
    public function count()
    {
        return $this->apply('count');
    }
}