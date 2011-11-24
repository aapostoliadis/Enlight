<?php
class Default_Controllers_Frontend_Index extends Enlight_Controller_Action
{	
	public function init()
	{
        //$this->View()->Engine()->registerFilter('pre', array($this, 'preFilter'));
		//Enlight_Application::Instance()->Plugins()->Controller()->ViewRenderer()->setNoRender();
	}

    public function preDispatch()
	{
    }
	
	public function indexAction()
	{
        $adapter =  new Enlight_Config_Adapter_File(array(
            'configType' => 'ini',
            'configDir' => Enlight_Application::Instance()->AppPath('Snippets')
        ));
        $snippets = new Enlight_Components_Snippet_Manager($adapter);
        $resource = new Enlight_Components_Snippet_Resource($snippets);
        $this->View()->Engine()->registerResource('snippet', $resource);
        $this->View()->Engine()->setDefaultResourceType('snippet');

        $path = Enlight_Application::Instance()->AppPath('DefaultViews');
        $this->View()->addTemplateDir($path);

        $this->View()->loadTemplate('frontend/index/index.tpl');
        //$this->View()->extendsTemplate('frontend/index/test.tpl');
        //$this->View()->extendsTemplate('frontend/index/test2.tpl');
        Enlight_Application::Instance()->Log()->debug('test');
		if($this->Request()->getPathInfo()!='/') {
			 $this->Response()->setHttpResponseCode(404);
		}
	}
}