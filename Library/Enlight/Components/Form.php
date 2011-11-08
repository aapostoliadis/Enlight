<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Form
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Form
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Form extends Zend_Form
{
	public function isValid($data)
	{
		return parent::isValid($data);
	}

	/**
	 * Converts the form to an array - so this array can be saved as a config object.
	 * The optional parameter section can be uses to segment the form
	 *
	 * @param null|string $section
	 * @return array
	 */
	public function toArray($section = 'default')
	{

		$data = array();
		// Get Form Header Files
		$attributes = $this->getAttribs();
		foreach($attributes as $key => $attribute)
		{
			$data[$key] = $attribute;
		}

		// Get Form Elements
		$elements = $this->getElements();
		foreach( $elements as $key=>$element){
			$data['elements'][$key] = $this->toArrayElement($element);
		}
		$retVal['decorators'] = $this->convertFormDecorators();
		$retVal['elementDecorators'] = $this->convertDecorators($element);

		if(!is_null($section) && is_string($section))
		{
			$retVal[$section] = $data;
		}
		else{
			$retVal = $data;
		}
		return $retVal;
	}


	/**
	 * Alias for Enlight_Form::getHtml()
	 *
	 * @return String HTML Code
	 */
	public function render()
	{
		return $this->getHtml();
	}


	/**
	 * Returns a form rendered as a html form. Keeps user from hassling with zend_view
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$this->setView(new Zend_View());
		$this->setAttrib('class','enlight_form');
		
		return parent::render();
	}

	/**
	 * Small helper method to clean up some reflection action on the form
	 *
	 * @param $name
	 * @return string
	 */
	private function getShortName($name)
	{
		if($name instanceof Zend_Form_Element) {
			$name = $name->getType();
		}
		if(is_object($name)) {
			$name = get_class($name);
		}
		return strtr($name, array('Zend_Form_Element_'=>'', 'Zend_Filter_'=>'', 'Zend_Validate_'=>''));
	}

	/**
	 * Transforms a Zend_Form_Element to an array
	 *
	 * @param $element Zend_Form_Element
	 * @return array
	 */
	private function toArrayElement($element)
	{
		$options = array(
			'description',
			'allowEmpty',
			'ignore',
			'order',
			'label',
			'value',
			'id',
			'name',
			'belongsTo',
			'attributes'
		);

		$arrayElement = array(
			'type' => $this->getShortName($element)
		);

		$arrayElement['label'] = $element->getLabel();

		// Handle Validators
		$arrayElement['options']['validators'] = $this->convertValidators($element);

		// Handle requirement
		if($element->isRequired()){
			$arrayElement['options']['required'] = $element->isRequired();
		}
		// Handle Filters
		$arrayElement['options']['filters'] = $this->convertFilters($element);
		
		// Handle decorators
		//$arrayElement['decorators'] = $this->convertElementDecorators($element);

		return $arrayElement;
	}
	
	/**
	 * Converts form decorators to an array
	 *
	 * @param $element
	 * @return array
	 */
	private function convertDecorators($element)
	{
		$decorators = $element->getDecorators();
		$retval = array();
		$cnt = 0;
		foreach($decorators as $decorator)		{
			$decorName = str_replace('Zend_Form_Decorator_','', get_class($decorator));
			$decorOptions = $decorator->getOptions();
			if( empty($decorOptions)){
				$retval[] = array($decorName);
			}
			else{

				$tmp = array($decorName,$decorOptions);
				$retval[]=$tmp;
				unset($tmp);
			}
		}
		return $retval;

	}

	/**
	 * Converts elements decorators to an array
	 *
	 * @param $element
	 * @return array
	 */
	private function convertElementDecorators($element)
	{
		$decorators = $element->getDecorators();
		$retval = array();
		$cnt = 0;
		foreach($decorators as $decorator)		{
			$decorName = str_replace('Zend_Form_Decorator_','', get_class($decorator));
			$decorOptions = $decorator->getOptions();
			if( empty($decorOptions)){
				$retval[] = array($decorName);
			}
			else{

				$tmp = array($decorName,$decorOptions);
				$retval[]=$tmp;
				unset($tmp);
			}
		}
		return $retval;

	}

	/**
	 * Converts form decorators to an array
	 *
	 * @return array
	 */
	private function convertFormDecorators()
	{
		$decorators = $this->getDecorators();//$element->getDecorators();
		$retval = array();
		$cnt = 0;
		foreach($decorators as $decorator)		{
			$decorName = str_replace('Zend_Form_Decorator_','', get_class($decorator));
			$decorOptions = $decorator->getOptions();
			if( empty($decorOptions)){
				$retval[] = array($decorName);
			}
			else{
				$tmp = array($decorName,$decorOptions);
				$retval[]=$tmp;
				unset($tmp);
			}
		}
		return $retval;

	}

	/**
	 * Converts form filters to an array
	 *
	 * @param $element Zend_Form_Element
	 * @return array
	 */
	private function convertFilters($element)
	{
		$retval = array();
		$filters = $element->getFilters();
		if($filters) {
			$arrayElement['options']['filters'] = array();
			foreach ($filters as $filterKey => $filter) {
				$retval[$this->getShortName($filter)]  = array('filter' => $this->getShortName($filter));
			}
		}
		return $retval;
	}

	/**
	 * Converts form validators to an array
	 *
	 * @param $element Zend_Form_Element
	 * @return array
	 */
	private function convertValidators($element)
	{
		$validators = $element->getValidators();
		$retval = array();
		if($validators) {
			$arrayElement['options']['validators'] = array();
			foreach ($validators as $validatorKey => $validator) {
				$array_validator = array('validator' => $this->getShortName($validator));
				$validator_options = $validator->getMessageVariables();
				if($validator_options) {
					$array_validator['options'] = array();
					foreach ($validator_options as $validator_option) {
						
						$value = $validator->$validator_option;
						if($value !== null) {
							$array_validator['options'][$validator_option] = $validator->$validator_option;
							$msgs = $validator->getMessageTemplates();
							$array_validator['options']['messages'] = $msgs;
							if($validator->zfBreakChainOnFailure){
								$array_validator['options']['breakChainOnFailure'] = true;
							}
						}
					}
				}
				$retval[$array_validator['validator']] = $array_validator;
			}
		}
		return $retval;
	}

	public function __construct($options)
	{
		parent::__construct($options);
	}
}