<?php
class Default_Controllers_Backend_Error extends Enlight_Controller_Action
{	
	public function preDispatch()
	{
		$this->Front()->getParam('controllerPlugins')->ViewRenderer()->setNoRender();
	}
	
	public function errorAction()
	{
		$error = $this->Request()->getParam('error_handler');

		$response = new Enlight_Controller_Response_ResponseCli();
		$response->appendBody(strip_tags($error->exception) . "\n");

		$this->front->setResponse($response);
	}
}