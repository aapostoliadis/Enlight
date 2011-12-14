<?php
class Default_Controllers_Frontend_Index extends Enlight_Controller_Action
{	
	public function init()
	{
        //$this->View()->Engine()->registerFilter('pre', array($this, 'preFilter'));
		//
    }

    public function preDispatch()
	{
        //Enlight_Application::Instance()->Plugins()->Controller()->Json()->setPadding();
        //Enlight_Application::Instance()->Plugins()->Controller()->Json()->setRenderer();
        $this->View()->setCaching(false);
    }
	
	public function indexAction()
	{
        /*
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

        //$this->View()->loadTemplate('frontend/index/index.tpl');
        //$this->View()->extendsTemplate('frontend/index/test.tpl');
        //$this->View()->extendsTemplate('frontend/index/test2.tpl');
        Enlight_Application::Instance()->Log()->debug('test');
		if($this->Request()->getPathInfo()!='/') {
			 $this->Response()->setHttpResponseCode(404);
		}
        */
	}

    public function menuAction()
    {
        $this->View()->Site = Enlight_Application::Instance()->Site();
    }

    public function loginAction()
	{
        //Enlight_Application::Instance()->Plugins()->Controller()->ViewRenderer()->setNoRender();

        //var_dump($this->Request()->getParams());

        /*
        $this->View()->loadTemplate('string:test');
        $this->View()->addCacheId('test');

        if(!$this->View()->isCached()) {
            //$count++;
            $this->View()->count += 1;
        }
        */
        //$this->View()->setCaching(true);

        $form = new Enlight_Components_Form();

        //$form->addPrefixPath('Enlight_Components', 'Enlight/Components/');

        $form->clearDecorators();
        $form->addDecorator('FormElements')
            ->addDecorator('Form');

        $form->addElement('text', 'username', array(
            'validators' => array(
                'alnum',
                array('regex', false, '/^[a-z]/i')
            ),
            'required' => true,
            'filters'  => array('StringToLower'),
        ));

        $form->addElement('textarea', 'text', array(
            'required' => true,
            'filters'  => array('StringToLower'),
            'label'    => 'Text'
        ));

        $form->addElement('radio', 'selection', array(
            'required' => true,
            'multiOptions' => array(1 =>'1',2 =>'2',3 =>'3',4 => '4',5 =>'5'),
            'Label' => 'Q1 text',
        ));

        $form->addElement('button',
            'delete',
            array(
                'label'    => 'Delete'
            )
        );

        $form->addElement('submit',
            'save',
            array(
                'label'    => 'Save and continue'
            )
        );

        //$form->addDisplayGroup(array('delete', 'save'), 'buttons');

        $this->View()->form = $form;
    }

    public function testAction()
	{
        
    }
}