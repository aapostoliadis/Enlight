<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * Test case
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
class Enlight_Tests_Form_FormTest extends Enlight_Components_Test_TestCase
{
	private $testFile = 'testForm';
    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();
    }

	/**
	 * Tearing down
	 *
	 * @return void
	 */
	public function tearDown()
	{
		// löschen der ConfigFiles
		$target = Enlight_TestHelper::Instance()->TestPath('TempFiles').'testForm.ini';
		$target2 = Enlight_TestHelper::Instance()->TestPath('TempFiles').'testForm_test.ini';
		$this->removeOldForm($target);
		if(file_exists($target2) && is_writable($target2))
		{
			//unlink($target2);
		}
	}

    /**
	 * Method to test if we are able to load an form config
	 *
	 * The first part is to create an defined config and write that on the disk.
	 * Supporting 
     */
    public function testLoadForm()
    {
		$this->writeConfig();
		$cfgAdapter = new Enlight_Config_Adapter_File( array('configType'=>'ini',
															 'configDir' => Enlight_TestHelper::Instance()->TestPath('TempFiles')) );
		$formCfg = new Enlight_Config($this->testFile,
												array(	'adapter'=>$cfgAdapter,
													 	'allowModifications' => true
												));
		$formCfg->read();

		$form = new Enlight_Components_Form($formCfg->toArray());
		$this->assertInstanceOf('Enlight_Components_Form',$form);
    }

	public function testLoadForm2()
	{
		$this->writeConfig();
		$cfgAdapter = new Enlight_Config_Adapter_File( array('configType'=>'ini',
															 'configDir' => Enlight_TestHelper::Instance()->TestPath('TempFiles')) );
		$formCfg = new Enlight_Config($this->testFile,
										array(	'adapter'=>$cfgAdapter,
												'allowModifications' => true
										));
		$formCfg->read();

		$form = new Enlight_Components_Form($formCfg->toArray());
		$this->assertInstanceOf('Enlight_Components_Form',$form);
	}

	/**
	 * Method to test if a form can be saved
	 * After we got the form from the helper method, we're converting the Form to an array.
	 * We expect that this array has a 'default/elements' part and that part is an array with 3 parts.
	 * The next step is to create an Enlight_Config_Adapter and a corresponding Enlight_Config container.
	 * This container is used to save the form to disk.
	 *
	 * @return void
	 */
	public function testSaveForm()
	{
		$cfgAdapter = new Enlight_Config_Adapter_File( array('configType'=>'ini', 'configDir' => Enlight_TestHelper::Instance()->TestPath('TempFiles')) );
		$formCfg = new Enlight_Config($this->testFile.'_test', array('adapter'=>$cfgAdapter,'allowModifications' => true));
		$form = $this->getForm();
		$form->setAdapter($formCfg);
		$form->setOptions(array('adapter'=>$formCfg));
		$form->save();

		$this->assertFileExists( Enlight_TestHelper::Instance()->TestPath('TempFiles').$this->testFile.'_test.ini');
		$this->assertContains('firstName.options.validators.NotEmpty.validator', file_get_contents(Enlight_TestHelper::Instance()->TestPath('TempFiles').$this->testFile.'_test.ini'));

	}

	/**
	 * Method to test if we are able to modify a form
	 * First, we are getting a standardized form from our helper method
	 * After we received the form, we check if the form contains the submit button. We could check for every element,
	 * but to save some time, we're just checking one element.
	 * Now it's time to do some changes, so we remove the submit button and we're adding a new Textfield to the
	 * Form.
	 *
	 * @return void
	 */
	public function testModifyForm()
	{
		$form = $this->getForm();

		// get submit button
		$testElement = $form->getElement('submit');
		$this->assertInstanceOf('Zend_Form_Element_Submit', $testElement);
		$testElement = null;

		// Remove submit button
		$this->assertTrue($form->removeElement('submit'));
		$this->assertNull($form->getElement('submit'));

		// Add new Element
		$newElement = new Zend_Form_Element_Text('email');
		$this->assertInstanceOf('Zend_Form_Element_Text', $newElement);
		$newElement->setLabel('EMail');
		$newElement->setName('email');
		$this->assertInstanceOf('Enlight_Components_Form', $form->addElement($newElement));

		// Get new text field
		$testElement = $form->getElement('email');
		$this->assertInstanceOf('Zend_Form_Element_Text', $testElement);
		$this->assertEquals($testElement->getLabel(),'EMail');
		$this->assertEquals($testElement->getName(), 'email');

		$this->assertInstanceOf('Enlight_Components_Form',$form->setElement('Text','email'));
	}

	/**
	 * Small helper method to build a form
	 *
	 * @param null $cfg
	 * @return Enlight_Components_Form
	 */
	private function getForm($cfg = null)
	{
		$form = new Enlight_Components_Form(array());

		$this->assertInstanceOf('Enlight_Components_Form',$form);

		$form->setDisableLoadDefaultDecorators(true);
		$form->setAction('login');
		$form->setName('contact us');

		$firstName = new Zend_Form_Element_Text('firstName');
		$this->assertInstanceOf('Zend_Form_Element_Text', $firstName);
		$firstName->setLabel('First Name')->setRequired(true)->addValidator('NotEmpty');

		$lastName = new Zend_Form_Element_Text('lastName');
		$this->assertInstanceOf('Zend_Form_Element_Text', $lastName);
		$lastName->setLabel('Last Name')->setRequired(true)->addValidator('NotEmpty')->addValidator('StringLength',false, array('min'=>6, 'max'=>10));
		$lastName->setFilters(array(New Zend_Filter_Alnum()));
		$lastName->setDecorators(array(new Zend_Form_Decorator_Label()));

		$submit = new Zend_Form_Element_Submit('submit');
		$this->assertInstanceOf('Zend_Form_Element_Submit', $submit);
		$submit->setLabel('Contact us');

		$form->addElements(array($firstName, $lastName, $submit));
		return $form;
	}

	/**
	 * Small helper method to write an form config
	 *
	 * @return void
	 */
	private function writeConfig()
	{
		$cfg = '
[testing]
; general form metainformation
user.login.action = "/user/login"
user.login.method = "post"

; username element
user.login.elements.username.type = "text"
user.login.elements.username.options.validators.alnum.validator = "alnum"
user.login.elements.username.options.validators.regex.validator = "regex"
user.login.elements.username.options.validators.regex.options.pattern = "/^[a-z]/i"
user.login.elements.username.options.validators.strlen.validator = "StringLength"
user.login.elements.username.options.validators.strlen.options.min = "6"
user.login.elements.username.options.validators.strlen.options.max = "20"
user.login.elements.username.options.required = true
user.login.elements.username.options.filters.lower.filter = "StringToLower"

; password element
user.login.elements.password.type = "password"
user.login.elements.password.options.validators.strlen.validator = "StringLength"
user.login.elements.password.options.validators.strlen.options.min = "6"
user.login.elements.password.options.required = true

; submit element
user.login.elements.submit.type = "submit"

user.login.disableLoadDefaultDecorators = true
user.login.decorators.formElements.decorator = "FormElements"
user.login.decorators.description.decorator = "Description"
user.login.decorators.form.decorator = "Form"
		';
		$target = Enlight_TestHelper::Instance()->TestPath('TempFiles').'testForm.ini';
		if(file_exists($target)){
			$this->removeOldForm($target);
		}
		file_put_contents( Enlight_TestHelper::Instance()->TestPath('TempFiles').'testForm.ini',$cfg);
	}

	private function removeOldForm($target)
	{
		if(is_writable($target)){
			//unlink($target);
		}
	}

}