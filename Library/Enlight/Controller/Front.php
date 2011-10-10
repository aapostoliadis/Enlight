<?php
class Enlight_Controller_Front extends Enlight_Class implements Enlight_Hook, Enlight_Singleton
{
	protected $_router;
	protected $_dispatcher;
	protected $_request;
	protected $_response;
	
	protected $_controller_path;
	
	protected $_throwExceptions;
	protected $_returnResponse;
	protected $_invokeParams = array();

	public function dispatch()
	{
		if (!$this->getParam('noErrorHandler')) {
        	$this->getParam('controllerPlugins')->ErrorHandler();
        }
        if (!$this->getParam('noViewRenderer')) {
        	$this->getParam('controllerPlugins')->ViewRenderer();
        }
		
		Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_StartDispatch', array('subject'=>$this));
	
		if(!$this->_router) {
			$this->setRouter('Enlight_Controller_Router_RouterDefault');
		}
		if(!$this->_dispatcher) {
			$this->setDispatcher('Enlight_Controller_Dispatcher_DispatcherDefault');
		}
		if(!$this->_request) {
			$this->setRequest('Enlight_Controller_Request_RequestHttp');
		}
		if(!$this->_response) {
			$this->setResponse('Enlight_Controller_Response_ResponseHttp');
		}
		
		try {
			
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
            * Notify plugins of router startup
            */
            Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_RouteStartup', array('subject'=>$this));
			
            try {
            	$this->_router->route($this->_request);
            } catch (Exception $e) {
                if ($this->throwExceptions()) {
                    throw $e;
                }
                $this->_response->setException($e);
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
             *  Attempt to dispatch the controller/action. If the $this->_request
             *  indicates that it needs to be dispatched, move to the next
             *  action in the request.
             */
            do {
                $this->_request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 */
                try {
                    Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_PreDispatch', array('subject'=>$this, 'request'=>$this->Request()));
                    
                    /**
	                 * Skip requested action if preDispatch() has reset it
	                 */
	                if (!$this->_request->isDispatched()) {
	                    continue;
	                }
	
	                /**
	                 * Dispatch request
	                 */
	                try {
	                    $this->_dispatcher->dispatch($this->_request, $this->_response);
	                } catch (Exception $e) {
	                    if ($this->throwExceptions()) {
	                        throw $e;
	                    }
	                    $this->_response->setException($e);
	                }
                    
                } catch (Exception $e) {
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->_response->setException($e);
                }
              
                /**
                 * Notify plugins of dispatch completion
                 */
                Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_PostDispatch', array('subject'=>$this, 'request'=>$this->Request()));
            }
            while (!$this->_request->isDispatched());
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->_response->setException($e);
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
            $this->_response->setException($e);
        }

        if ($this->returnResponse()) {
            return $this->_response;
        }

        if(!Enlight_Application::Instance()->Events()->notifyUntil('Enlight_Controller_Front_SendResponse', array('subject'=>$this, 'response'=>$this->Response(), 'request'=>$this->Request()))) {
	    	$this->Response()->sendResponse();
	    }
        
        Enlight_Application::Instance()->Events()->notify('Enlight_Controller_Front_AfterSendResponse', array('subject'=>$this, 'request'=>$this->Request()));
	}
	
	public function setRouter ($router)
	{
		if (is_string($router)) {
            $router = new $router();
        }
        if (!$router instanceof Enlight_Controller_Router_Router) {
            throw new Enlight_Exception('Invalid router class');
        }
        $router->setFront($this);
        $this->_router = $router;
		return $this;
	}
	public function setDispatcher ($dispatcher)
	{
		if (is_string($dispatcher))
		{
            $dispatcher = new $dispatcher();
        }
        if (!$dispatcher instanceof Enlight_Controller_Dispatcher_Dispatcher)
        {
            throw new Enlight_Exception('Invalid dispatcher class');
        }
        $dispatcher->setFront($this);
        $this->_dispatcher = $dispatcher;
		return $this;
	}
	public function setRequest ($request)
	{
		if (is_string($request))
		{
            $request = new $request();
        }
        if (!$request instanceof Enlight_Controller_Request_Request)
        {
            throw new Enlight_Exception('Invalid request class');
        }
        $this->_request = $request;
		return $this;
	}
	public function setResponse ($response)
	{		
		if (is_string($response))
		{
            $response = new $response();
        }
        if (!$response instanceof Enlight_Controller_Response_Response)
        {
            throw new Enlight_Exception('Invalid response class');
        }
        $this->_response = $response;
		return $this;
	}
	
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->_returnResponse = true;
            return $this;
        } elseif (false === $flag) {
            $this->_returnResponse = false;
            return $this;
        }
        return $this->_returnResponse;
    }
	
    /**
     * Enter description here...
     *
     * @return Enlight_Controller_Router_RouterDefault
     */
	public function Router()
	{
		return $this->_router;
	}
	/**
	 * Enter description here...
	 *
	 * @return Enlight_Controller_Request_RequestHttp
	 */
	public function Request()
	{
		return $this->_request;
	}
	/**
	 * Enter description here...
	 *
	 * @return Enlight_Controller_Response_ResponseHttp
	 */
	public function Response()
	{
		if(!$this->_response) {
			$this->setResponse('Enlight_Controller_Response_ResponseHttp');
		}
		return $this->_response;
	}
	/**
	 * Enter description here...
	 *
	 * @return Enlight_Controller_Dispatcher_DispatcherDefault
	 */
	public function Dispatcher()
	{
		if (!$this->_dispatcher) {
            $this->setDispatcher('Enlight_Controller_Dispatcher_DispatcherDefault');
        }
		return $this->_dispatcher;
	}
			
	public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->_throwExceptions = (bool) $flag;
            return $this;
        }

        return $this->_throwExceptions;
    }
    
	public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->_invokeParams[$name] = $value;
        return $this;
    }
    public function setParams(array $params)
    {
        $this->_invokeParams = array_merge($this->_invokeParams, $params);
        return $this;
    }
    public function getParam($name)
    {
        if(isset($this->_invokeParams[$name]))
        {
            return $this->_invokeParams[$name];
        }
        return null;
    }
    public function getParams()
    {
        return $this->_invokeParams;
    }
}