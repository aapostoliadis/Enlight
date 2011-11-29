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
 * @package    Enlight_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Bootstrap extends Enlight_Class implements Enlight_Hook
{
	const STATUS_BOOTSTRAP = 0;
	const STATUS_LOADED = 1;
	const STATUS_NOT_FOUND = 2;
	const STATUS_ASSIGNED = 3;
	
	/**
	 * Resource list
	 *
	 * @var array
	 */
    protected $resourceList = array();
    
    /**
     * Resource status list
     *
     * @var array
     */
    protected $resourceStatus = array();

	/**
     * Application instance.
     *
     * @var Enlight_Application
     */
    protected $application;

    /**
	 * Constructor
     *
     * Sets application object, initializes options, and prepares list of
     * initializer methods.
	 *
	 * @param Enlight_Application $application
	 */
    public function __construct(Enlight_Application $application)
    {
        $this->setApplication($application);
        parent::__construct();
        //$options = $application->getOptions();
        //$this->setOptions($options);
    }

	/**
     * Sets the application instance.
     *
     * @param  Enlight_Application $application
     * @return Enlight_Bootstrap
     */
    public function setApplication(Enlight_Application $application)
    {
		$this->application = $application;
        return $this;
    }

	/**
     * Returns the application instance.
     *
     * @return Enlight_Application
     */
    public function Application()
    {
        return $this->application;
    }
    
    /**
     * Run application method
     * 
     * @return mixed
     */
    public function run()
    {
    	/** @var $front Enlight_Controller_Front */
		$front = $this->getResource('Front');
        return $front->dispatch();
    }
    
    /**
     * Init front method
     *
     * @return Enlight_Controller_Front
     */
    protected function initFront()
    {
    	$this->loadResource('Zend');

        /** @var $front Enlight_Controller_Front */
    	$front = Enlight_Class::Instance('Enlight_Controller_Front');

   	    $front->Dispatcher()->addModuleDirectory(
			$this->Application()->AppPath('Controllers')
		);
   	    
   	    $config = $this->Application()->getOption('Front');
		if($config !== null) {
			$front->setParams($config);
		}

        $namespace = new Enlight_Plugin_Namespace_Loader('Controller');
        $namespace->addPrefixPath(
            'Enlight_Controller_Plugins',
            $this->Application()->CorePath('Controller_Plugins')
        );
        $this->Application()->Plugins()->registerNamespace($namespace);

		$front->setParam('controllerPlugins', $namespace);
		$front->setParam('bootstrap', $this);
    	    	
    	if(!empty($config['throwExceptions'])) {
    		$front->throwExceptions(true);
    	}
    	if(!empty($config['returnResponse'])) {
    		$front->returnResponse(true);
    	}
   	    
        return $front;
    }

    /**
     * Init template method
     *
     * @return Enlight_Template_Manager
     */ 
    protected function initTemplate()
    {
        /** @var $template Enlight_Template_Manager */
    	$template = Enlight_Class::Instance('Enlight_Template_Manager');

		$template->setCompileDir($this->Application()->AppPath('Cache_Compiles'));
		$template->setCacheDir($this->Application()->AppPath('Cache_Templates'));
		$template->setTemplateDir($this->Application()->AppPath('Views'));

		$config = $this->Application()->getOption('template');
		if($config !== null) {
			foreach ($config as $key => $value) {
				$template->{'set' . $key}($value);
			}
		}
		
        return $template;
    }

    /**
     * Init zend method
     *
     * @return bool
     */
    protected function initZend()
    {
    	$this->Application()->Loader()->registerNamespace('Zend', 'Zend/');
		//$this->Application()->Loader()->addIncludePath(
		//		$this->Application()->Path(), Enlight_Loader::POSITION_PREPEND
		//);
    	return true;
    }
    
    /**
     * Register resource method
     *
     * @param string $name
     * @param mixed $resource
     * @return Enlight_Bootstrap
     */
    public function registerResource($name, $resource)
    {
    	$this->resourceList[$name] = $resource;
    	$this->resourceStatus[$name] = self::STATUS_ASSIGNED;
    	return $this;
    }
    
    /**
     * Has resource method
     *
     * @param string $name
     * @return bool
     */
    public function hasResource($name)
    {
    	return isset($this->resourceList[$name])||$this->loadResource($name);
    }
    
    /**
     * Returns resource method
     *
     * @param string $name
     * @return bool
     */
    public function issetResource($name)
    {
    	return isset($this->resourceList[$name]);
    }
    
    /**
     * Returns resource method
     *
     * @param string $name
     * @return Enlight_Class
     */
    public function getResource($name)
    {
        if(!isset($this->resourceStatus[$name])) {
        	$this->loadResource($name);
        }
    	if($this->resourceStatus[$name]===self::STATUS_NOT_FOUND) {
    		throw new Enlight_Exception('Resource "'.$name.'" not found failure');
    	}
    	return $this->resourceList[$name];
    }

    /**
     * Load resource method
     *
     * @param string $name
     * @return bool
     */
    public function loadResource($name)
    {
    	if(isset($this->resourceStatus[$name])) {
    		switch ($this->resourceStatus[$name]) {
    			case self::STATUS_BOOTSTRAP:
    				throw new Enlight_Exception('Resource "'.$name.'" can\'t resolve all dependencies');
    			case self::STATUS_NOT_FOUND:
    				return false;
    			case self::STATUS_ASSIGNED:
    			case self::STATUS_LOADED:
    				return true;
    			default:
    				break;
    		}
    	}
    	
    	try {
	    	$this->resourceStatus[$name] = self::STATUS_BOOTSTRAP;
	    	if($event = $this->Application()->Events()->notifyUntil('Enlight_Bootstrap_InitResource_'.$name, array('subject'=>$this))) {
		    	$this->resourceList[$name] = $event->getReturn();
		    } elseif(method_exists($this, 'init'.$name)) {
		    	$this->resourceList[$name] = call_user_func(array($this, 'init'.$name));
		    }
			$this->Application()->Events()->notify('Enlight_Bootstrap_AfterInitResource_'.$name, array('subject'=>$this));
    	} catch (Exception $e) {
    		$this->resourceStatus[$name] = self::STATUS_NOT_FOUND;
	    	throw $e;
    	}
	    
	    if(isset($this->resourceList[$name])&&$this->resourceList[$name]!==null) {
	    	$this->resourceStatus[$name] = self::STATUS_LOADED;
	    	return true;
	    } else {
	    	$this->resourceStatus[$name] = self::STATUS_NOT_FOUND;
	    	return false;
	    }
    }
    
    /**
     * Reset resource method
     *
     * @param string $name
     * @return Enlight_Bootstrap
     */
    public function resetResource($name)
    {
    	if(isset($this->resourceList[$name])) {
	    	unset($this->resourceList[$name]);
	    	unset($this->resourceStatus[$name]);
    	}
    	return $this;
    }

    /**
     * Returns called resource
     *
     * @param string $name
     * @param array $arguments
     * @return Enlight_Class Resource
     */
    public function __call ($name, $arguments=null)
    {
    	return $this->getResource($name);
    }
}