<?php
class Enlight_Controller_Plugins_ViewRenderer_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
    /**
     * @var bool
     */
	protected $neverRender = false;

    /**
     * @var bool
     */
    protected $noRender = false;

	/**
	 * @var Enlight_Controller_Front
	 */
    protected $front;

    /**
     * @var Enlight_Controller_Action
     */
    protected $action;

    /**
     * @var Enlight_View_ViewDefault
     */
    protected $view;

    /**
     * @return void
     */
	public function init()
	{
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Front_DispatchLoopStartup',
	 		400,
            array($this, 'onDispatchLoopStartup')
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Action_PostDispatch',
            400,
	 		array($this, 'onPostDispatch')
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Action_PreDispatch',
            400,
	 		array($this, 'onPreDispatch')
	 	);
		Enlight_Application::Instance()->Events()->registerListener($event);
		$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Action_Init',
            400,
	 		array($this, 'onActionInit')
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

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatch(Enlight_Event_EventArgs $args)
	{
		if ($this->shouldRender() && $this->View()->hasTemplate()) {
	        $this->render();
	    }
	    $this->View()->setTemplate();
	}

    /**
     * @param Enlight_Event_EventArgs $args
     */
	public function onPreDispatch(Enlight_Event_EventArgs $args)
	{
		if($this->shouldRender()&&!$this->View()->hasTemplate())
		{
			$this->View()->loadTemplate($this->getTemplateName());
		}
	}

    /**
     * @param Enlight_Event_EventArgs $args
     */
	public function onActionInit(Enlight_Event_EventArgs $args)
	{
		$this->action = $args->getSubject();
		$this->initView();
	}
    
    public function initView()
    {
    	$view = Enlight_Application::Instance()->Bootstrap()->getResource('View');
		$this->setView($view);
		
    	$this->View()->setTemplate();
    	$this->Action()->setView($this->view);
    }

    /**
     * @param   string $template
     * @param   string $name
     * @return void
     */
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