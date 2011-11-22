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
class Enlight_Controller_Front extends Enlight_Class implements Enlight_Hook, Enlight_Singleton
{
    /**
     * @var Enlight_Controller_Router_RouterDefault
     */
	protected $router;

    /**
     * @var Enlight_Controller_Dispatcher_Dispatcher
     */
	protected $dispatcher;

    /**
     * @var Enlight_Controller_Request_RequestHttp
     */
	protected $request;

    /**
     * @var Enlight_Controller_Response_ResponseHttp
     */
	protected $response;

    /**
     * @var bool
     */
	protected $throwExceptions;

    /**
     * @var bool
     */
	protected $returnResponse;

    /**
     * @var array
     */
	protected $invokeParams = array();

    /**
     * @throws  Exception
     * @return  Enlight_Controller_Response_ResponseHttp
     */
	public function dispatch()
	{
		if (!$this->getParam('noErrorHandler')) {
        	$this->getParam('controllerPlugins')->ErrorHandler();
        }
        if (!$this->getParam('noViewRenderer')) {
        	$this->getParam('controllerPlugins')->ViewRenderer();
        }
		if (!$this->getParam('jsonRenderer')){
			$this->getParam('controllerPlugins')->Json();
		}
		
		Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_StartDispatch', array('subject'=>$this));
	
		if(!$this->router) {
			$this->setRouter('Enlight_Controller_Router_RouterDefault');
		}
		if(!$this->dispatcher) {
			$this->setDispatcher('Enlight_Controller_Dispatcher_DispatcherDefault');
		}
		if(!$this->request) {
			$this->setRequest('Enlight_Controller_Request_RequestHttp');
		}
		if(!$this->response) {
			$this->setResponse('Enlight_Controller_Response_ResponseHttp');
		}
		
		try {
		    /**
              * Notify plugins of router startup
              */
            Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_RouteStartup', array('subject'=>$this));

            /**
             * Route request to controller/action, if a router is provided
             */
            try {
            	$this->router->route($this->request);
            } catch (Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }
                $this->response->setException($e);
            }
			
            /**
             * Notify plugins of router completion
             */
            Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_RouteShutdown', array('subject'=>$this, 'request'=>$this->Request()));

            /**
             * Notify plugins of dispatch loop startup
             */
            Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_DispatchLoopStartup', array('subject'=>$this, 'request'=>$this->Request()));

            /**
             *  Attempt to dispatch the controller/action. If the $this->request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                try {
                    Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_PreDispatch', array('subject'=>$this, 'request'=>$this->Request()));
                    
                    /**
	                 * Skip requested action if preDispatch() has reset it
	                 */
	                if (!$this->request->isDispatched()) {
	                    continue;
	                }
	
	                /**
	                 * Dispatch request
	                 */
	                try {
	                    $this->dispatcher->dispatch($this->request, $this->response);
	                } catch (Exception $e) {
	                    if ($this->throwExceptions()) {
	                        throw $e;
	                    }
	                    $this->response->setException($e);
	                }
                    
                } catch (Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->response->setException($e);
                }
              
                /**
                 * Notify plugins of dispatch completion
                 */
                Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_PostDispatch', array('subject'=>$this, 'request'=>$this->Request()));
            }
            while (!$this->request->isDispatched());
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->response->setException($e);
        }
		
        /**
         * Notify plugins of dispatch loop completion
         */
		try {
            Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_DispatchLoopShutdown', array('subject'=>$this));
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->response;
        }

        if(!Enlight_Application::Instance()->Events()->notifyUntil('Enlight_Controller_Front_SendResponse', array('subject'=>$this, 'response'=>$this->Response(), 'request'=>$this->Request()))) {
	    	$this->Response()->sendResponse();
	    }
        
        Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_AfterSendResponse', array('subject'=>$this, 'request'=>$this->Request()));
	}

    /**
     * @throws  Enlight_Exception
     * @param   $router
     * @return  Enlight_Controller_Front
     */
	public function setRouter ($router)
	{
		if (is_string($router)) {
            $router = new $router();
        }
        if (!$router instanceof Enlight_Controller_Router_Router) {
            throw new Enlight_Exception('Invalid router class');
        }
        $router->setFront($this);
        $this->router = $router;
		return $this;
	}

    /**
     * @throws  Enlight_Exception
     * @param   $dispatcher
     * @return  Enlight_Controller_Front
     */
	public function setDispatcher ($dispatcher)
	{
		if (is_string($dispatcher)) {
            $dispatcher = new $dispatcher();
        }
        if (!$dispatcher instanceof Enlight_Controller_Dispatcher_Dispatcher) {
            throw new Enlight_Exception('Invalid dispatcher class');
        }
        $dispatcher->setFront($this);
        $this->dispatcher = $dispatcher;
		return $this;
	}

    /**
     * @throws  Enlight_Exception
     * @param   $request
     * @return  Enlight_Controller_Front
     */
	public function setRequest ($request)
	{
		if (is_string($request)) {
            $request = new $request();
        }
        if (!$request instanceof Enlight_Controller_Request_Request) {
            throw new Enlight_Exception('Invalid request class');
        }
        $this->request = $request;
		return $this;
	}

    /**
     * Sets the response instance
     *
     * @throws  Enlight_Exception
     * @param   $response
     * @return  Enlight_Controller_Front
     */
	public function setResponse ($response)
	{		
		if (is_string($response)) {
            $response = new $response();
        }
        if (!$response instanceof Enlight_Controller_Response_Response) {
            throw new Enlight_Exception('Invalid response class');
        }
        $this->response = $response;
		return $this;
	}

    /**
     * Sets the return response flag
     * Returns the value of the return response flag
     *
     * @param   null $flag
     * @return  bool|Enlight_Controller_Front
     */
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->returnResponse = true;
            return $this;
        } elseif (false === $flag) {
            $this->returnResponse = false;
            return $this;
        }
        return $this->returnResponse;
    }
	
    /**
     * Returns the router instance.
     *
     * @return  Enlight_Controller_Router_RouterDefault
     */
	public function Router()
	{
		return $this->router;
	}
    
	/**
	 * Returns the request instance.
	 *
	 * @return  Enlight_Controller_Request_RequestHttp
	 */
	public function Request()
	{
		return $this->request;
	}
    
	/**
	 * Returns the response instance.
	 *
	 * @return  Enlight_Controller_Response_ResponseHttp
	 */
	public function Response()
	{
		if($this->response === null) {
			$this->setResponse('Enlight_Controller_Response_ResponseHttp');
		}
		return $this->response;
	}
    
	/**
	 * Enter description here...
	 *
	 * @return  Enlight_Controller_Dispatcher_DispatcherDefault
	 */
	public function Dispatcher()
	{
		if ($this->dispatcher === null) {
            $this->setDispatcher('Enlight_Controller_Dispatcher_DispatcherDefault');
        }
		return $this->dispatcher;
	}

    /**
     * @param   bool|null $flag
     * @return  bool|Enlight_Controller_Front
     */
	public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->throwExceptions = (bool) $flag;
            return $this;
        }
        return $this->throwExceptions;
    }

    /**
     * @param   $name
     * @param   $value
     * @return  Enlight_Controller_Front
     */
	public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->invokeParams[$name] = $value;
        return $this;
    }

    /**
     * @param   array $params
     * @return  Enlight_Controller_Front
     */
    public function setParams(array $params)
    {
        $this->invokeParams = array_merge($this->invokeParams, $params);
        return $this;
    }

    /**
     * Sets a invoke param by name.
     * 
     * @param   $name
     * @return  mixed
     */
    public function getParam($name)
    {
        if(isset($this->invokeParams[$name])) {
            return $this->invokeParams[$name];
        }
        return null;
    }

    /**
     * Returns the list of invoked params.
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->invokeParams;
    }
}