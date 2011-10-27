<?php
class Default_Controllers_Frontend_Index extends Enlight_Controller_Action
{	
	public function preDispatch()
	{
		//Enlight_Application::Instance()->Plugins()->Controller()->ViewRenderer()->setNoRender();
	}
	
	public function indexAction()
	{
        Enlight_Application::Instance()->Log()->debug('test');

		if($this->Request()->getPathInfo()!='/') {
			 $this->Response()->setHttpResponseCode(404);
		}
	}
}