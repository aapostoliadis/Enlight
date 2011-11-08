<?php
class Default_Controllers_Frontend_Index extends Enlight_Controller_Action
{	
	public function preDispatch()
	{
		//Enlight_Application::Instance()->Plugins()->Controller()->ViewRenderer()->setNoRender();
	}
	
	public function indexAction()
	{
        //Enlight_Application::Instance()->Log()->debug('test');
		if($this->Request()->getPathInfo()!='/') {
			 $this->Response()->setHttpResponseCode(404);
		}
	}

	public function loginAction()
	{
		//Enlight_Application::Instance()->Log()->debug('called login action.');
		$this->view->assign('status','Status: ---');
		include 'Enlight/Apps/Default/Forms/contactForm.php';
		$login = new contactForm();
		
		if($this->request->isPost())
		{
			$formData = $this->request->getPost();
			if($login->isValid($formData))
			{
				$this->view->assign('status','Status: OK');
			}
			else
			{
				$login->populate($formData);
				$this->view->assign('status','Status: Error');
			}
		}

		$this->view->assign('loginForm', $login->getHtml());
	}
}