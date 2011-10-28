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
class Enlight_Controller_Plugins_ErrorHandler_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
    /**
     * @return void
     */
	public function init()
	{
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Front_RouteShutdown',
            500,
	 		array($this, 'onRouteShutdown')
	 	);
		$this->Application()->Events()->registerListener($event);
        
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Front_PostDispatch',
	 		500,
            array($this, 'onPostDispatch')
	 	);
		$this->Application()->Events()->registerListener($event);
	}

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return  void
     */
	public function onRouteShutdown(Enlight_Event_EventArgs $args)
	{
		$this->handleError($args->getSubject(), $args->getRequest());
	}

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return  void
     */
	public function onPostDispatch(Enlight_Event_EventArgs $args)
	{
		$this->handleError($args->getSubject(), $args->getRequest());
	}
	
	const EXCEPTION_NO_CONTROLLER = 'EXCEPTION_NO_CONTROLLER';

    /**
     * Const - No action exception; controller exists, but action does not
     */
    const EXCEPTION_NO_ACTION = 'EXCEPTION_NO_ACTION';

    /**
     * Const - No route exception; no routing was possible
     */
    const EXCEPTION_NO_ROUTE = 'EXCEPTION_NO_ROUTE';

    /**
     * Const - Other Exception; exceptions thrown by application controllers
     */
    const EXCEPTION_OTHER = 'EXCEPTION_OTHER';
    
    /**
     * Flag; are we already inside the error handler loop?
     *
     * @var bool
     */
    protected $_isInsideErrorHandlerLoop = false;

    /**
     * Exception count logged at first invocation of plugin
     *
     * @var int
     */
    protected $_exceptionCountAtFirstEncounter = 0;

    /*
     * @var     Enlight_Controller_Front $front
     * @var     Enlight_Controller_Request_Request $request
     */
	protected function handleError($front, Enlight_Controller_Request_Request $request)
    {
        if ($front->getParam('noErrorHandler')) {
            return;
        }

        $response = $front->Response();

        if ($this->_isInsideErrorHandlerLoop) {
            $exceptions = $response->getException();
            if (count($exceptions) > $this->_exceptionCountAtFirstEncounter) {
                // Exception thrown by error handler; tell the front controller to throw it
                $front->throwExceptions(true);
                throw array_pop($exceptions);
            }
        }
        
        // check for an exception AND allow the error handler controller the option to forward
        if (($response->isException()) && (!$this->_isInsideErrorHandlerLoop)) {
            $this->_isInsideErrorHandlerLoop = true;

            // Get exception information
            $error            = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $exceptions       = $response->getException();
            $exception        = $exceptions[0];
            $error->exception = $exception;
            switch (true) {
                case $exception instanceof Zend_Controller_Router_Exception:
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ROUTE;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                case $exception instanceof Zend_Controller_Dispatcher_Exception:
                    $error->type = self::EXCEPTION_NO_CONTROLLER;
                    break;
                case $exception instanceof Zend_Controller_Action_Exception:
                    if (404 == $exception->getCode()) {
                        $error->type = self::EXCEPTION_NO_ACTION;
                    } else {
                        $error->type = self::EXCEPTION_OTHER;
                    }
                    break;
                default:
                    $error->type = self::EXCEPTION_OTHER;
                    break;
            }

            // Keep a copy of the original request
            $error->request = clone $request;

            // get a count of the number of exceptions encountered
            $this->_exceptionCountAtFirstEncounter = count($exceptions);
            
            // Forward to the error handler
            $request->setParam('error_handler', $error)
                    ->setControllerName('error')
                    ->setActionName('error')
                    ->setDispatched(false);
                    //->setModuleName($this->getErrorHandlerModule())
                    //->setControllerName($this->getErrorHandlerController())
                    //->setActionName($this->getErrorHandlerAction())
        }
    }
}