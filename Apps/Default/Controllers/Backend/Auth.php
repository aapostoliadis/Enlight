<?php
class Default_Controllers_Backend_Auth extends Enlight_Controller_Action
{
    public function init()
    {
        $this->Front()->Plugins()->JsonRequest()
            ->setParseInput()
            ->setParseParams(array('group', 'sort'));
        $this->Front()->Plugins()->ScriptRenderer()->setRender();
    }

	public function preDispatch()
	{
        if(!in_array($this->Request()->getActionName(), array('index', 'load'))) {
            $this->Front()->Plugins()->Json()->setRenderer();
        }
        $this->View()->setCaching(true);
	}
	
	public function indexAction()
	{
	}

    public function loadAction()
    {
    }

    public function getUsersAction()
    {
        $this->View()->group = $this->Request()->getParam('group');
        $this->View()->sort = $this->Request()->getParam('sort');
        $this->View()->success = true;
        $this->View()->data = array(
            array(
                'id' => '48', 'username' => 'demo', 'password' => '84c2ef7bb215395c80119636233765f0',
                'sessionID' => 'ccbco9tagg7gipap85j8rqvnb6', 'lastlogin' => '2011-12-29 12:36:47',
                'name' => 'Administrator', 'email' => 'info@shopware.de', 'active' => '1', 'admin' => '1'
            )
        );
        $this->View()->total = 1;
    }

    public function createUserAction()
    {
        $this->View()->success = false;
        $this->View()->errorMsg = 'test';
    }
}