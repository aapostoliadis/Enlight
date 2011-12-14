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
abstract class Enlight_Controller_Dispatcher extends Enlight_Class
{
    /**
     * @var Enlight_Controller_Front
     */
	protected $front;

    /**
     * @var Enlight_Controller_Response_Response
     */
	protected $response;

    /**
     * @param   Enlight_Controller_Front $controller
     * @return  Enlight_Controller_Dispatcher
     */
	public function setFront(Enlight_Controller_Front $controller)
    {
        $this->front = $controller;
        return $this;
    }

    /**
     * @return Enlight_Controller_Front
     */
    public function Front()
    {
        return $this->front;
    }

    /**
     * @param   Enlight_Controller_Response_Response|null $response
     * @return  Enlight_Controller_Dispatcher
     */
    public function setResponse(Enlight_Controller_Response_Response $response = null)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Enlight_Controller_Response_Response
     */
    public function Response()
    {
        return $this->response;
    }

    /**
     * @abstract
     * @param Enlight_Controller_Request_Request $request
     * @param Enlight_Controller_Response_Response $response
     */
    abstract public function dispatch(Enlight_Controller_Request_Request $request, Enlight_Controller_Response_Response $response);
}