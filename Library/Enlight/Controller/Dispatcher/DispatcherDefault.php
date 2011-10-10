<?php
class Enlight_Controller_Dispatcher_DispatcherDefault extends Enlight_Controller_Dispatcher_Dispatcher
{
	/**
     * Current dispatchable directory
     * @var string
     */
    protected $curDirectory;

    /**
     * Current module (formatted)
     * @var string
     */
    protected $curModule;
    
	/**
     * Default action
     * @var string
     */
    protected $defaultAction = 'index';

    /**
     * Default controller
     * @var string
     */
    protected $defaultController = 'index';

    /**
     * Default module
     * @var string
     */
    protected $defaultModule = 'frontend';

    /**
     * Front Controller instance
     * @var Zend_Controller_Front
     */
    protected $frontController;

    /**
     * Path delimiter character
     * @var string
     */
    protected $pathDelimiter = '_';

    /**
     * Word delimiter characters
     * @var array
     */
    protected $wordDelimiter = array('-', '.');
    
    /**
     * Controller directory(ies)
     * @var array
     */
    protected $controllerDirectory = array();
	
	public function addControllerDirectory($path, $module = null)
	{
		if(empty($module))
		{
			$module = $this->defaultModule;
		}
		$module = $this->formatModuleName($module);
        $path = realpath($path).'/';

        $this->controllerDirectory[$module] = $path;
        
        return $this;
	}
	
	public function setControllerDirectory($directory, $module = null)
	{
		$this->controllerDirectory = array();

        if (is_string($directory)) {
            $this->addControllerDirectory($directory, $module);
        } else {
            foreach ((array) $directory as $module => $path) {
                $this->addControllerDirectory($path, $module);
            }
        }
        
        return $this;
	}
	
	public function getControllerDirectory($module = null)
	{
		if(empty($module)) return $this->controllerDirectory;
		$module = $this->formatModuleName($module);
		return isset($this->controllerDirectory[$module]) ? $this->controllerDirectory[$module] : null;
	}
	
	public function removeControllerDirectory($module)
	{
		$module = (string) $module;
		if(isset($this->controllerDirectory[$module]))
		{
			unset($this->controllerDirectory[$module]);
			return  true;
		}
		else
		{
			return false;
		}
	}
	
	public function addModuleDirectory($path)
    {
        try {
            $dir = new DirectoryIterator($path);
        } catch(Exception $e) {
            throw new Enlight_Controller_Exception("Directory $path not readable", 0, $e);
        }
        foreach ($dir as $file) {
            if ($file->isDot() || !$file->isDir()) {
                continue;
            }

            $module    = $file->getFilename();

            // Don't use SCCS directories as modules
            if (preg_match('/^[^a-z]/i', $module) || ('CVS' == $module)) {
                continue;
            }

            $moduleDir = $file->getPathname();
            $this->addControllerDirectory($moduleDir, $module);
        }

        return $this;
    }
	
	public function formatControllerName($unformatted)
    {
        return str_replace('_', '', $this->formatName($unformatted));
    }
    
    public function formatActionName($unformatted)
    {
        return str_replace('_', '', $this->formatName($unformatted));
    }
	
	public function formatModuleName($unformatted)
	{
		return ucfirst($this->formatName($unformatted));
	}
	
	protected function formatName($unformatted, $isAction = false)
    {
        if (!$isAction) {
            $segments = explode($this->pathDelimiter, $unformatted);
        } else {
            $segments = (array) $unformatted;
        }

        foreach ($segments as $key => $segment)
        {
        	$segment = preg_replace('#[A-Z]#', ' $0', $segment);
            $segment  = str_replace($this->wordDelimiter, ' ', strtolower($segment));
            $segment  = preg_replace('/[^a-z0-9 ]/', '', $segment);
            $segments[$key] = str_replace(' ', '', ucwords($segment));
        }

        return implode('_', $segments);
    }
    
    public function setDefaultControllerName($controller)
    {
        $this->defaultController = (string) $controller;
        return $this;
    }
    
    public function getDefaultControllerName()
    {
        return $this->defaultController;
    }
    
    public function setDefaultAction($action)
    {
        $this->defaultAction = (string) $action;
        return $this;
    }
    
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }
    
    public function setDefaultModule($module)
    {
        $this->defaultModule = (string) $module;
        return $this;
    }
    
    public function getDefaultModule()
    {
        return $this->defaultModule;
    }
    
	public function getControllerClass(Enlight_Controller_Request_Request $request)
	{
		if(!$request->getControllerName())
		{
			$request->setControllerName($this->defaultController);
		}
		if(!$request->getModuleName())
		{
			$request->setModuleName($this->defaultModule);
		}
		
		$module = $request->getModuleName();
		$this->curModule = $module;
		$this->curDirectory = $this->getControllerDirectory($module);
		
		$moduleName = $this->formatModuleName($module);
		$controllerName = $this->formatControllerName($request->getControllerName());
		
		$class = array(Enlight_Application::Instance()->App(), 'Controllers', $moduleName, $controllerName);
		$class = implode('_', $class);
		return $class;
	}
	public function getControllerPath(Enlight_Controller_Request_Request $request)
	{
		$controllerName = $request->getControllerName();
		$controllerName = $this->formatControllerName($controllerName);
		$moduleName = $this->formatModuleName($this->curModule);
		if($event = Enlight_Application::Instance()->Events()->notifyUntil('Enlight_Controller_Dispatcher_ControllerPath_'.$moduleName.'_'.$controllerName, array('subject'=>$this, 'request'=>$request))) {
	    	$path = $event->getReturn();
	    } else {
	    	$path = $this->curDirectory.$controllerName.'.php';
	    }
		return $path;
	}
	public function getActionMethod(Enlight_Controller_Request_Request $request)
	{
		$action = $request->getActionName();
        if (empty($action)) {
            $action = $this->getDefaultAction();
            $request->setActionName($action);
        }
        $formatted = $this->formatActionName($action);
		$formatted = strtolower(substr($formatted, 0, 1)) . substr($formatted, 1) . 'Action';
        return $formatted;
	}
	public function getFullControllerName(Enlight_Controller_Request_Request $request)
	{
		$parts = array(
			$this->formatModuleName($request->getModuleName()),
			$this->formatControllerName($request->getControllerName())
		);
		return implode('_', $parts);
	}
	public function getFullActionName(Enlight_Controller_Request_Request $request)
	{
		$parts = array(
			$this->formatModuleName($request->getModuleName()),
			$this->formatControllerName($request->getControllerName()),
			$this->formatActionName($request->getActionName())
		);
		return implode('_', $parts);
	}
	
	public function isDispatchable(Enlight_Controller_Request_Request $request)
    {
    	$className = $this->getControllerClass($request);
        if (!$className) {
            return false;
        }
        if (class_exists($className, false)) {
            return true;
        }
        $path = $this->getControllerPath($request);
        return Enlight_Loader::isReadable($path);
    }
    
    public function isValidModule($module)
    {
        if (!is_string($module)) {
            return false;
        }

        $controllerDir = $this->getControllerDirectory($module);
        
        return !empty($controllerDir);
    }
	
	public function dispatch(Enlight_Controller_Request_Request $request, Enlight_Controller_Response_Response $response)
	{
		$this->setResponse($response);
		
		if (!$this->isDispatchable($request))
		{
            $controller = $request->getControllerName();
            if (!$this->Front()->getParam('useDefaultControllerAlways') && !empty($controller))
            {
                throw new Enlight_Controller_Exception('Controller "'.$controller.'" not found', Enlight_Controller_Exception::Controller_Dispatcher_Controller_Not_Found);
            }
            $request->setControllerName($this->defaultController);
        }
		
        $class = $this->getControllerClass($request);
        $path = $this->getControllerPath($request);
                
        try
        {
        	Enlight_Application::Instance()->Loader()->loadClass($class, $path);
    	}
    	catch (Exception $e)
    	{
            throw new Enlight_Exception('Controller "'.$class.'" can\'t load failure');
    	}

    	$proxy = Enlight_Application::Instance()->Hooks()->getProxy($class);
    	$controller = new $proxy($request, $response);

    	$action =  $this->getActionMethod($request);

    	$request->setDispatched(true);
    	
    	$disableOb = $this->Front()->getParam('disableOutputBuffering');
        $obLevel = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }
    	
    	try {
    		$controller->dispatch($action);
    	} catch (Exception $e) {
    		$curObLevel = ob_get_level();
    		if ($curObLevel > $obLevel) {
    			do {
    				ob_get_clean();
    				$curObLevel = ob_get_level();
    			} while ($curObLevel > $obLevel);
    		}
    		throw $e;
    	}

    	if (empty($disableOb)) {
    		$content = ob_get_clean();
    		$response->appendBody($content);
    	}
	}
}