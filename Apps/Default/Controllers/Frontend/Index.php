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
        $this->View()->Template()->cache_lifetime = 1000;
        $this->View()->setScope(Smarty::SCOPE_GLOBAL);
    }
	
	public function indexAction()
	{
	}

    public function menuAction()
    {
        if(!$this->View()->isCached()) {
            //$this->View()->Site = Enlight_Application::Instance()->Site();
        }
    }

    public function loginAction()
	{
        $form = new Enlight_Components_Form();

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

        if($this->Request()->isPost()) {
            $formData = $this->Request()->getPost();
            if(!$form->isValid($formData)) {
                $form->populate($formData);
            }
        }

        //$form->addDisplayGroup(array('delete', 'save'), 'buttons');

        $this->View()->form = $form;
    }

    public function testAction()
	{
        
    }
}