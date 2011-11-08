<?php
/**
 * Enlight Controller Action
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 */
abstract class Enlight_Controller_Action extends Enlight_Class implements Enlight_Hook
{
	/**
	 * @var Enlight_Controller_Front
	 */
	protected $front;
	/**
	 * @var Enlight_View_ViewDefault
	 */
	protected $view;
	/**
	 * @var Enlight_Controller_Request_Request
	 */
	protected $request;
	/**
	 * @var Enlight_Controller_Response_Response
	 */
	protected $response;
	
	/**
	 * @var string
	 */
	protected $controller_name;
	
	/**
	 * Constructor method
	 */
	public function __construct(Enlight_Controller_Request_Request $request, Enlight_Controller_Response_Response $response)
	{
		$this->setRequest($request)->setResponse($response);
		
		$this->controller_name = $this->Front()->Dispatcher()->getFullControllerName($this->Request());

		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_Init', array('subject'=>$this, 'request'=>$this->Request(), 'response'=>$this->Response()));
		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_Init_'.$this->controller_name, array('subject'=>$this, 'request'=>$this->Request(), 'response'=>$this->Response()));
		
		parent::__construct();
	}
	
	/**
	 * Pre dispatch method
	 */
	public function preDispatch()
	{
		
	}
    
	/**
	 * Post dispatch method
	 */
	public function postDispatch()
	{
		
	}

	/**
	 * Dispatch action method
	 * 
	 * @param $action string
	 */
	public function dispatch($action)
	{
		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_PreDispatch', array('subject'=>$this,'request'=>$this->Request()));
		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_PreDispatch_'.$this->controller_name, array('subject'=>$this, 'request'=>$this->Request()));
		$this->preDispatch();
		
		if ($this->Request()->isDispatched()&&!$this->Response()->isRedirect()) {
			$action_name = $this->Front()->Dispatcher()->getFullActionName($this->Request());
			if(!$event = Enlight_Application::Instance()->Events()->notifyUntil(__CLASS__.'_'.$action_name, array('subject'=>$this))) {
		    	$this->$action();
		    }
			$this->postDispatch();
		}
		
		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_PostDispatch_'.$this->controller_name, array('subject'=>$this, 'request'=>$this->Request()));
		Enlight_Application::Instance()->Events()->notify(__CLASS__.'_PostDispatch', array('subject'=>$this,'request'=>$this->Request()));
	}
	
	/**
	 * Forward request method
	 *
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 * @param array $params
	 */
	public function forward($action, $controller = null, $module = null, array $params = null)
    {
		$request = $this->Request();
		
        if($params !== null) {
            $request->setParams($params);
        }
        if($controller !== null) {
            $request->setControllerName($controller);
            if($module !== null) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)->setDispatched(false);
    }
    
    /**
     * Redirect request
     *
     * @param string|array $url
     * @param array $options
     */
    public function redirect($url, array $options = array())
    {
    	if (is_array($url)) {
    		$url = $this->Front()->Router()->assemble($url);
    	}
    	if(!preg_match('#^(https?|ftp)://#', $url)) {
    		if(strpos($url, '/') !== 0) {
    			$url = $this->Request()->getBaseUrl().'/'.$url;
    		}
    		$uri = $this->Request()->getScheme().'://'.$this->Request()->getHttpHost();
    		$url = $uri.$url;
    	}
    	$this->Response()->setRedirect($url, empty($options['code']) ? 302 : (int) $options['code']);
    }

	/**
	 * Set view instance
	 *
	 * @param Enlight_View_View $view
	 * @return Enlight_Controller_Action
	 */
	public function setView (Enlight_View_View $view)
	{
		$this->view = $view;
		return $this;
	}
		
	/**
	 * Set request instance
	 *
	 * @param Enlight_Controller_Request_Request $request
	 * @return Enlight_Controller_Action
	 */
	public function setRequest (Enlight_Controller_Request_Request $request)
	{
        $this->request = $request;
		return $this;
	}
	
	/**
	 * Set response instance
	 *
	 * @param Enlight_Controller_Response_Response $response
	 * @return Enlight_Controller_Action
	 */
	public function setResponse (Enlight_Controller_Response_Response $response)
	{
        $this->response = $response;
		return $this;
	}
	
	/**
	 * Returns view instance
	 *
	 * @return Enlight_View_ViewDefault
	 */
	public function View()
	{
		if($this->view===null) {
			$this->view = Enlight::Instance()->Bootstrap()->getResource('View');
		}
		return $this->view;
	}
	
	/**
	 * Returns front controller
	 *
	 * @return Enlight_Controller_Front
	 */
	public function Front()
	{
		if($this->front===null){
			$this->front = Enlight_Class::Instance('Enlight_Controller_Front');
		}
		return $this->front;
	}
	
	/**
	 * Returns request instance
	 *
	 * @return Enlight_Controller_Request_RequestHttp
	 */
	public function Request()
	{
		return $this->request;
	}
	
	/**
	 * Returns response instance
	 *
	 * @return Enlight_Controller_Response_ResponseHttp
	 */
	public function Response()
	{
		return $this->response;
	}
	
	/**
	 * Magic caller method
	 *
	 * @param string $name
	 * @param array $value
	 * @return mixed
	 */
	public function __call($name, $value=null)
    {
        if ('Action' == substr($name, -6)) {
            $action = substr($name, 0, strlen($name) - 6);
            throw new Enlight_Controller_Exception('Action "'.$this->controller_name.'_'.$name.'" not found failure', Enlight_Controller_Exception::ActionNotFound);
        }
        return parent::__call($name, $value);
    }
}