<?php
class Enlight_Controller_Plugins_ViewRenderer_Bootstrap extends Enlight_Plugin_PluginBootstrap
{
	public function init()
	{
		$event = new Enlight_Event_EventHandler(
	 		'Enlight_Controller_Front_DispatchLoopStartup',
	 		array($this, 'onDispatchLoopStartup'),
	 		400
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_EventHandler(
	 		'Enlight_Controller_Action_PostDispatch',
	 		array($this, 'onPostDispatch'),
	 		400
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_EventHandler(
	 		'Enlight_Controller_Action_PreDispatch',
	 		array($this, 'onPreDispatch'),
	 		400
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_EventHandler(
	 		'Enlight_Controller_Action_Init',
	 		array($this, 'onActionInit'),
	 		400
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
	}
	
	public function onDispatchLoopStartup(Enlight_Event_EventArgs $args)
	{
		if ($args->getSubject()->getParam('noViewRenderer')) {
            return;
        }
        $this->front = $args->getSubject();
	}
	    
    public function onPostDispatch(Enlight_Event_EventArgs $args)
	{
		if ($this->shouldRender()&&$this->View()->hasTemplate()) {
	        $this->render();
	    }
	    $this->View()->setTemplate();
	}
	
	public function onPreDispatch(Enlight_Event_EventArgs $args)
	{
		if($this->shouldRender()&&!$this->View()->hasTemplate())
		{
			$this->View()->loadTemplate($this->getTemplateName());
		}
	}
	
	public function onActionInit(Enlight_Event_EventArgs $args)
	{
		$this->action = $args->getSubject();
		$this->initView();
	}
	
	protected $neverRender = false;
	
    protected $noRender = false;
	
    protected $responseSegment = null;

	/**
	 * @var Enlight_Controller_Front
	 */
    protected $front;
    
    protected $action;
    
    protected $view;
    
    public function initView()
    {
    	$view = Enlight_Application::Instance()->Bootstrap()->getResource('View');
		$this->setView($view);
		
    	$this->View()->setTemplate();
    	$this->Action()->setView($this->view);
    }
		    
    public function renderTemplate($template, $name = null)
    {
    	Enlight_Application::Instance()->Events()->notify('Enlight_Plugins_ViewRenderer_PreRender', array('subject'=>$this, 'template'=>$template));
    	
    	$render = $this->View()->render($template);
    	$render = Enlight_Application::Instance()->Events()->filter('Enlight_Plugins_ViewRenderer_FilterRender', $render, array('subject'=>$this, 'template'=>$template));
		
        $this->Front()->Response()->appendBody(
            $render,
            $name
        );
        $this->setNoRender();
        
        Enlight_Application::Instance()->Events()->notify('Enlight_Plugins_ViewRenderer_PostRender', array('subject'=>$this));
    }

    public function render()
    {
    	$template = $this->View()->Template();
        $this->renderTemplate($template);
    }

    protected function shouldRender()
    {
        return (!$this->Front()->getParam('noViewRenderer')
            && !$this->neverRender
            && !$this->noRender
            && $this->Action()
            && $this->Action()->Request()->isDispatched()
            && !$this->Action()->Response()->isRedirect()
        );
    }
    
    public function Front()
	{
		return $this->front;
	}
	
	public function Action()
	{
		return $this->action;
	}
	
	public function View()
	{
		return $this->view;
	}
	
	public function setView($view)
	{
		$this->view = $view;
		return $this;
	}
	
	public function setNoRender($flag = true)
	{
		$this->noRender = $flag ? true : false;;
		return $this;
	}
	
	public function setNeverRender($flag = true)
    {
        $this->neverRender = $flag ? true : false;
        return $this;
    }
    
    public function getTemplateName()
	{
		$request = $this->Action()->Request();
		$moduleName = $this->Front()->Dispatcher()->formatModuleName($request->getModuleName());
		$controllerName = $this->Front()->Dispatcher()->formatControllerName($request->getControllerName());
		$actionName = $this->Front()->Dispatcher()->formatActionName($request->getActionName());
		
		$parts = array($moduleName, $controllerName, $actionName);
		foreach ($parts as &$part)
		{
			$part = preg_replace('#[A-Z]#', '_$0', $part);
			$part = trim($part, '_');
			$part = strtolower($part);
		}
		
		$templateName = implode(DIRECTORY_SEPARATOR, $parts).'.tpl';
		return $templateName;
	}
}