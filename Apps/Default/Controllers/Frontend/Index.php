<?php
class Default_Controllers_Frontend_Index extends Enlight_Controller_Action
{
	public function indexAction()
	{
		if($this->Request()->getPathInfo()!='/') {
			 $this->Response()->setHttpResponseCode(404);
		}
	}
}