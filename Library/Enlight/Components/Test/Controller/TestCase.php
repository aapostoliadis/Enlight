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
 * @package    Enlight_Test
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Test
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Components_Test_Controller_TestCase extends Enlight_Components_Test_TestCase
{
	/**
     * @var Enlight_Controller_Front
     */
    protected $_front;
    
    /**
     * @var Enlight_Template_Manager
     */
    protected $_template;
    
    /**
     * @var Enlight_View_Default
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
        
        Enlight_Application::Instance()->Bootstrap()
        	->resetResource('Session')
        	->resetResource('Auth');
        
        //$this->Front()
        //     ->setRequest($this->Request())
        //     ->setResponse($this->Response());
    }
    
    /**
     * Dispatch the request
     *
     * @param   string|null $url
     * @return  Zend_Controller_Response_Abstract
     */
    public function dispatch($url = null)
    {
        $request    = $this->Request();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $response = $this->Response();
        
        $this->Front()
             ->setRequest($request)
             ->setResponse($response);

        //return Enlight_Application::Instance()->run();

        $dispatcher = $this->Front()->Dispatcher();

        $dispatcher->dispatch($request, $response);

        return $response;
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

        Enlight_Application::Instance()->Plugins()->resetPlugins();
        Enlight_Application::Instance()->Hooks()->resetHooks();
        Enlight_Application::Instance()->Events()->resetEvents();
        
        Enlight_Application::Instance()->Db()->getProfiler()->clear();
        
        $resources = array(
        	'Plugins' => 'Enlight_Plugin_PluginManager',
        	'Template' => 'Enlight_Template_TemplateManager',
        	'Front' => 'Enlight_Controller_Front',
        	'Enlight_Controller_Plugins_ErrorHandler_Bootstrap',
        	'Enlight_Controller_Plugins_ViewRenderer_Bootstrap'
        );
        
        foreach ($resources as $resource => $class) {
        	Enlight_Class::resetInstance($class);
        	if(!is_int($resource)) {
        		Enlight_Application::Instance()->Bootstrap()
        			->resetResource($resource)
        			->loadResource($resource);
        	}
        }
        
        Enlight_Application::Instance()->Bootstrap()
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
            $this->_front = Enlight_Application::Instance()->Bootstrap()->getResource('Front');
        }
        return $this->_front;
    }
    
    /**
     * Retrieve template instance
     *
     * @return Enlight_Template_Manager
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
     * @return Enlight_View_Default
     */
    public function View()
    {
        if (null === $this->_view) {
            $this->_view = new Enlight_View_Default($this->Template());
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
     * @return mixed
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