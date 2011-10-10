<?php
/**
 * Controller test case
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
abstract class Enlight_Components_Test_Controller_TestCase extends Enlight_Components_Test_TestCase
{
	/**
     * @var Zend_Controller_Front
     */
    protected $_front;
    
    /**
     * @var Enlight_Template_TemplateManager
     */
    protected $_template;
    
    /**
     * @var Enlight_View_ViewDefault
     */
    protected $_view;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response;
    
    /**
     * Tests set up method
     */
	public function setUp()
    {
    	parent::setUp();
    	
        $this->reset();
        
        Enlight::Instance()->Bootstrap()
        	->resetResource('Session')
        	->resetResource('Auth');
        
        //$this->Front()
        //     ->setRequest($this->Request())
        //     ->setResponse($this->Response());
    }
    
    /**
     * Dispatch the request
     *
     * @param unknown_type $url
     * @return unknown
     */
    public function dispatch($url = null)
    {
        $request    = $this->Request();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);
        
        $this->Front()
             ->setRequest($request)
             ->setResponse($this->Response());

        return Enlight::Instance()->run();
    }
    
    /**
     * Reset all instances
     */
    public function reset()
    {		
    	$this->resetRequest();
        $this->resetResponse();
        
        $this->_view = null;
        $this->_template = null;
        $this->_front = null;
        
        foreach(Enlight::Instance()->Plugins()->getList() as $namespace) {
        	foreach($namespace->getList() as $plugin) {
	        	Enlight_Class::resetInstance($plugin);
	        }
        }
        
        Enlight::Instance()->Plugins()->resetPlugins();
        Enlight::Instance()->Hooks()->resetHooks();
        Enlight::Instance()->Events()->resetEvents();
        
        Enlight::Instance()->Db()->getProfiler()->clear();
        
        $ressources = array(
        	'Plugins' => 'Enlight_Plugin_PluginManager',
        	'Template' => 'Enlight_Template_TemplateManager',
        	'Front' => 'Enlight_Controller_Front',
        	'View' => 'Enlight_View_ViewDefault',
        	'Enlight_Controller_Plugins_ErrorHandler_Bootstrap',
        	'Enlight_Controller_Plugins_ViewRenderer_Bootstrap'
        );
        
        foreach ($ressources as $ressource => $class) {
        	Enlight_Class::resetInstance($class);
        	if(!is_int($ressource)) {
        		Enlight::Instance()->Bootstrap()
        			->resetResource($ressource)
        			->loadResource($ressource);
        	}
        }
        
        Enlight::Instance()->Bootstrap()
        	->resetResource('System')
        	->resetResource('Modules')
        	->resetResource('Config')
        	->resetResource('Shop');
    }
            
    /**
     * Reset the request object
     *
     * @return Enlight_Test_ControllerTestCase
     */
    public function resetRequest()
    {
        if ($this->_request instanceof Enlight_Controller_Request_RequestTestCase) {
            $this->_request->clearQuery()
                           ->clearPost()
                           ->clearCookies();
        }
        $this->_request = null;
        return $this;
    }

    /**
     * Reset the response object
     *
     * @return Enlight_Test_ControllerTestCase
     */
    public function resetResponse()
    {
        $this->_response = null;
        return $this;
    }
    
    /**
     * Retrieve front controller instance
     *
     * @return Enlight_Controller_Front
     */
    public function Front()
    {
        if (null === $this->_front) {
            $this->_front = Enlight::Instance()->Bootstrap()->getResource('Front');
        }
        return $this->_front;
    }
    
    /**
     * Retrieve template instance
     *
     * @return Enlight_Template_TemplateManager
     */
    public function Template()
    {
        if (null === $this->_template) {
            $this->_template = Enlight::Instance()->Bootstrap()->getResource('Template');
        }
        return $this->_template;
    }
    
    /**
     * Retrieve view instance
     *
     * @return Enlight_View_ViewDefault
     */
    public function View()
    {
        if (null === $this->_view) {
            $this->_view = Enlight::Instance()->Bootstrap()->getResource('View');
        }
        return $this->_view;
    }
    
    /**
     * Retrieve test case request object
     *
     * @return Enlight_Controller_Request_RequestTestCase
     */
    public function Request()
    {
        if (null === $this->_request) {
            $this->_request = new Enlight_Controller_Request_RequestTestCase;
        }
        return $this->_request;
    }

    /**
     * Retrieve test case response object
     *
     * @return Enlight_Controller_Response_ResponseHttp
     */
    public function Response()
    {
        if (null === $this->_response) {
            $this->_response = new Enlight_Controller_Response_ResponseTestCase;
        }
        return $this->_response;
    }
    
    /**
     * Magic get method
     * 
     * @param mixed $name
     * @return void
     */
    public function __get($name)
    {
        switch ($name) {
            case 'request':
                return $this->Request();
            case 'response':
                return $this->Response();
            case 'front':
            case 'frontController':
                return $this->Front();
        }
        return null;
    }
}