<?php
class contactForm extends Enlight_Components_Form
{

	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAction('login');
		$this->setName('contact us');

		$title = new Zend_Form_Element_Select('title');
		$title->setLabel('Title')
				->setMultiOptions(array('mr'=>'Mr', 'mrs'=>'Mrs'))
				->setRequired(true)->addValidator('NotEmpty', true);
		$firstName = new Zend_Form_Element_Text('firstName');
		$firstName->setLabel('First Name')->setRequired(true)->addValidators(
			array(
				 array('validator'=>'notEmpty',
						'options'=>array(
							'messages'=>array(
						   		Zend_Validate_NotEmpty::IS_EMPTY=>'Emtpy!')
							)
						),
						array(
							'validator'   => 'Regex',
							'breakChainOnFailure' => true,
							'options'     => array(
								'pattern' => '/^[+]?[-\d() .]*$/i',
								'messages' => array(
									Zend_Validate_Regex::NOT_MATCH =>'Look at my horse, my horse is amazing!'
								 ))

						 )
					)
			);


		$lastName = new Zend_Form_Element_Text('lastName');
		$lastName->setLabel('Last Name')->setRequired(true)->addValidator('NotEmpty');

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('email')
				->setLabel('EMail address')
				->addFilter('StringToLower')
				->setRequired(true)
				->addValidator('NotEmpty',true)
				->addValidator('EmailAddress');

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Contact us');
				
		$this->addElements(array($title, $firstName, $lastName, $email, $submit));
		$this->clearDecorators();
		$this->addDecorator('FormElements')
				->addDecorator('HtmlTag', array('tag' => '<ul>'))
				->addDecorator('Form');

		$this->setElementDecorators(array(
			array('ViewHelper'),
			array('Errors'),
			array('Description'),
			array('Label', array('separator'=>' ')),
			array('HtmlTag', array('tag' => 'li', 'class'=>'element-group')),
		));


	}
}