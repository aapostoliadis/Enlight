<?php
class
	Enlight_Controller_Router_RouterDefault extends Enlight_Controller_Router_Router
{
	protected $front;
	protected $globalParams = array();
	protected $seperator = '/';

	public function route(Zend_Controller_Request_Abstract $request)
	{
		$routeMatched = false;
		
		if($event = Enlight_Application::Instance()->Events()->notifyUntil('Enlight_Controller_Router_Route', array('subject'=>$this, 'request'=>$request))) {
	    	$params = $event->getReturn();
	    	$routeMatched = true;
	    } else {
	    	$params = $this->routeDefault($request);
	    	$routeMatched = true;
	    }
	   
	    if(!$routeMatched) {
	    	throw new Enlight_Controller_Exception('No route matched the request', Enlight_Controller_Exception::No_Route);
	    }
	    
	    $params = Enlight_Application::Instance()->Events()->filter('Enlight_Controller_Router_FilterRouteParams', $params);
	    
	    if($params!==null) {
	    	$this->setRequestParams($request, $params);
	    }
	    
	    return $request;
	}
	
	public function routeDefault(Zend_Controller_Request_Abstract $request)
	{
		$path = $request->getPathInfo();
		if(empty($path)) return;
		
		$dispatcher = $this->front->Dispatcher();
		
		$query = array(); $params = array();
		foreach (explode($this->seperator, $path) as $routePart)
		{
			$routePart = urldecode($routePart);
			if(empty($query[$request->getModuleKey()])&&$dispatcher->isValidModule($routePart))
			{
				$query[$request->getModuleKey()] = $routePart;
			}
			elseif(empty($query[$request->getControllerKey()]))
			{
				$query[$request->getControllerKey()] = $routePart;
			}
			elseif(empty($query[$request->getActionKey()])) 
			{
				$query[$request->getActionKey()] = $routePart;
			}
			else
			{
				$params[] = $routePart;
			}
		}
		if ($params)
		{
			$chunks = array_chunk($params,2,false);
			foreach ($chunks as $chunk)
			{
				$query[$chunk[0]] = $chunk[1];
			}
		}
		return $query;
	}
		
	public function assemble($userParams = array())
    {
    	if(is_string($userParams))
    	{
    		$userParams = parse_url($userParams, PHP_URL_QUERY);
			parse_str($userParams, $userParams);
    	}
    	
    	$request = $this->front->Request();
    	
    	Enlight()->Events()->notify('Enlight_Controller_Router_PreAssemble', array('subject'=>$this, 'request'=>$request));

    	$params = array_merge($this->globalParams, $userParams);
    	
    	$params = Enlight()->Events()->filter('Enlight_Controller_Router_FilterAssembleParams', $params, array('subject'=>$this, 'request'=>$request));
    	
    	if($event = Enlight()->Events()->notifyUntil('Enlight_Controller_Router_Assemble', array('subject'=>$this, 'params'=>$params, 'userParams'=>$userParams))) {
	    	$url = $event->getReturn();
	    } else {
	    	$url = $this->assembleDefault($params);
	    }
	    
	    $url = Enlight()->Events()->filter('Enlight_Controller_Router_FilterUrl', $url, array('subject'=>$this, 'params'=>$params, 'userParams'=>$userParams));
	    
	    if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($request->getBaseUrl(), '/') . '/' . $url;
        }
        
	    return $url;
    }
    
    public function assembleDefault($params = array())
    {
    	$request = $this->front->Request();
    	
    	$route = array();
    	
    	$module = isset($params[$request->getModuleKey()]) ? $params[$request->getModuleKey()] : '';
    	$controller = isset($params[$request->getControllerKey()]) ? $params[$request->getControllerKey()] : 'index';
    	$action = isset($params[$request->getActionKey()]) ? $params[$request->getActionKey()] : 'index';
    	
    	unset($params[$request->getModuleKey()], $params[$request->getControllerKey()], $params[$request->getActionKey()]);
    	
    	if($module!='frontend') {
    		$route[] = urlencode($module);
    	}
    	if(count($params)>0||$controller!='index'||$action!='index') {
    		$route[] = urlencode($controller);
    	}
    	if(count($params)>0||$action!='index') {
    		$route[] = urlencode($action);
    	}

    	foreach ($params as $key=>$value)
    	{
    		$route[] = urlencode($key);
    		$route[] = urlencode($value);
    	}
    	return implode($this->seperator, $route);
    } 
    
    public function setGlobalParam($name, $value)
    {
        $this->globalParams[$name] = $value;
        return $this;
    }
    
    protected function setRequestParams($request, $params)
    {
        foreach ($params as $param => $value)
        {
            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }
        }
    }
}