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
class Enlight_Tests_Form_FormRendererTest extends Enlight_Components_Test_TestCase
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
		parent::tearDown();
	}

	public function testGetHtml()
	{
		$original =str_replace("\r",'','<form id="contactus" name="contactus" enctype="application/x-www-form-urlencoded" action="login" class="enlight_form" method="post"><dl class="zend_form">
<dt id="firstName-label"><label for="firstName" class="required">First Name</label></dt>
<dd id="firstName-element">
<input type="text" name="firstName" id="firstName" value=""></dd>
<dt id="lastName-label"><label for="lastName" class="required">Last Name</label></dt>
<dd id="lastName-element">
<input type="text" name="lastName" id="lastName" value=""></dd>
<dt id="submit-label">&#160;</dt><dd id="submit-element">
<input type="submit" name="submit" id="submit" value="Contact us"></dd></dl></form>');
		$form = $this->getForm();
		$html = $form->getHtml();
		$this->assertEquals($original, $html);
	}


	/**
	 * Small helper class to get a form
	 *
	 * @return Enlight_Components_Form
	 */
	private function getForm()
	{
		$form = new Enlight_Components_Form(array());
		$this->assertInstanceOf('Enlight_Components_Form', $form);

		$form->setAction('login');
		$form->setName('contact us');

		$firstName = new Zend_Form_Element_Text('firstName');
		$this->assertInstanceOf('Zend_Form_Element_Text', $firstName);
		$firstName->setLabel('First Name')->setRequired(true)->addValidator('NotEmpty');

		$lastName = new Zend_Form_Element_Text('lastName');
		$this->assertInstanceOf('Zend_Form_Element_Text', $lastName);
		$lastName->setLabel('Last Name')->setRequired(true)->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Submit('submit');
		$this->assertInstanceOf('Zend_Form_Element_Submit', $submit);
		$submit->setLabel('Contact us');

		$form->addElements(array($firstName, $lastName, $submit));
		return $form;
	}
}