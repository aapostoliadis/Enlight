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
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Controller_Router extends Enlight_Class
{
    /**
     * @var Enlight_Controller_Front
     */
	protected $front;

    /**
     * @var array
     */
    protected $globalParams = array();
	
	/**
	 * Set front controller
	 *
	 * @param Enlight_Controller_Front $front
	 * @return Enlight_Controller_Router_Router
	 */
	public function setFront(Enlight_Controller_Front $front)
    {
        $this->front = $front;
        return $this;
    }
    
    /**
     * Returns front controller
     *
     * @return Enlight_Controller_Front
     */
    public function Front()
    {
    	return $this->front;
    }

    /**
     * @throws Enlight_Controller_Exception
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Request_Abstract|Zend_Controller_Request_Http
     */
    abstract public function route(Zend_Controller_Request_Abstract $request);

    /**
     * @param array $userParams
     * @return mixed|string
     */
    abstract public function assemble($userParams = array());

    /**
     * @param $name
     * @param $value
     * @return Enlight_Controller_Router_RouterDefault
     */
    public function setGlobalParam($name, $value)
    {
        $this->globalParams[$name] = $value;
        return $this;
    }
}