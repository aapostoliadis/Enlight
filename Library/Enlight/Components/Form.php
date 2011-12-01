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

	/**
	 * @var Integer
	 */
	protected $_id = null;

	/**
	 * @var Enlight_Config
	 */
	protected $_saverHandler = null;

	/**
	 * @param array|\Enlight_Config $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
		if($options instanceof Enlight_Config) {
			$this->setOptions($options->toArray());
		}elseif(is_array($options)){
			$this->setOptions($options);
		}

	}

	/**
	 * Saves the Form using an Enlight_Config_Adapter to do so.
	 * This is a rudimentary implementation and should be considered as beta
	 *
	 * @return void
	 */
	public function save()
	{
		$this->_saverHandler->setData($this->toArray());
		$this->_saverHandler->write( new Enlight_Config($this->toArray()));
	}

	/**
	 * Alias for the save method to keep a uniform interface through out enlight.
	 */
	public function write()
	{
		$this->save();
	}

	/**
	 * This Method extends the common Zend_Form setOption Method to add the additional parameter
	 * adapter - Contains a Enlight_Config_Adapter which is used to write the Form to the config
	 *
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		foreach($options as $optionName => $option){
			switch($optionName){
				case 'adapter':
					$this->setAdapter($option);
					break 2; // leave switch and the foreach loop
			}
		}
		reset($options);
		parent::setOptions($options);
	}

	/**
	 * Sets the write and read adapter
	 *
	 * @param Enlight_Config $adapter
	 */
	public function setAdapter(Enlight_Config $adapter)
	{
		$this->_saverHandler = $adapter;
	}

	/**
	 * Checks if a submitted form is valid
	 *
	 * @param $data
	 * @return bool
	 */
	public function isValid($data)
	{
		return parent::isValid($data);
	}

	/**
	 * Retrieves all decorators assigned to the given element
	 *
	 * @return array|null
	 */
	protected function getElementDecorators()
	{
		return $this->_elementDecorators;
	}

	/**
	 * Sets an new form element. This method will try to delete the form element first to avoid
	 * conflicts
	 *
	 * @param $element
	 * @param $name
	 * @param null $options
	 * @return Zend_Form
	 */
	public function setElement($element, $name, $options = null)
	{
		$this->removeElement($name);
		return $this->addElement($element, $name, $options);
	}
	public function setId($id)
	{
		$this->_attribs['id'] = $id;
		return $this;
	}

	/**
	 * Converts the form to an array - so this array can be saved as a config object.
	 * The optional parameter section can be uses to segment the form
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		// Get Form Header Files
		$attributes = $this->getAttribs();
		foreach($attributes as $key => $attribute) {
			$data[$key] = $attribute;
		}
		if(!empty($this->_disableLoadDefaultDecorators)) {
			$data['disableLoadDefaultDecorators'] = $this->_disableLoadDefaultDecorators;
		}
		// Get Form Elements
		$elements = $this->getElements();
		
		foreach($elements as $key=>$element){
			$data['elements'][$key] = $this->toArrayElement($element);
		}
		$data['elementDecorators'] = $this->convertElementDecorators($this->getElementDecorators());
		$data['decorators'] = $this->convertFormDecorators();

		return $data;
	}


	/**
     * Returns a form rendered as a html form. Keeps user from hassling with zend_view
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
	/*
    public function render(Zend_View_Interface $view = null)
	{
		if (null === $view) {
			$this->setView(new Zend_View());
		}
		$this->setAttrib('class','enlight_form');
		return parent::render();
	}
*/
	/**
	 * Small helper method to clean up some reflection action on the form
	 *
	 * @param $name
	 * @return string
	 */
	protected function getShortName($name)
	{
		if($name instanceof Zend_Form_Element) {
			/** @var $name Zend_Form_Element */
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
		$arrayElement = array(
			'type' => lcfirst($this->getShortName($element))
		);
		$label = $element->getLabel();
		if(!empty($label)) {
			$arrayElement['label'] = $label;
		}

		// Handle Validators
		$arrayElement['options']['validators'] = $this->convertValidators($element);

		// Handle requirement
		if($element->isRequired()){
			$arrayElement['options']['required'] = $element->isRequired();
		}
		// Handle Filters
		$arrayElement['options']['filters'] = $this->convertFilters($element);

		return $arrayElement;
	}
	
	/**
	 * Converts form decorators to an array
	 *
	 * @param Zend_Form_Element $element
	 * @return array
	 */
	private function convertDecorators($element)
	{
		$decorators = $element->getDecorators();
		$retVal = array();

		foreach($decorators as $decorator) {
			$decorName = str_replace('Zend_Form_Decorator_','', get_class($decorator));
			$decorOptions = $decorator->getOptions();
			if( empty($decorOptions)){
				$retVal[] = array($decorName);
			} else {
				$tmp = array($decorName,$decorOptions);
				$retVal[]=$tmp;
				unset($tmp);
			}
		}
		return $retVal;
	}

	/**
	 * Converts elements decorators to an array
	 *
	 * @param $elementDecorators
	 * @return array
	 */
	private function convertElementDecorators($elementDecorators)
	{
		$retVal = array();
			if(!empty($elementDecorators)){
			foreach($elementDecorators as $decorKey => $decorator) {
				if(!is_array($decorator))
				{
					$retVal[$decorKey] = $decorator;
				} else {
					foreach($decorator as $key=>$value) {
						$retVal[$decorKey][$key] = $value;
					}
				}
			}
		}
		return $retVal;
	}

	/**
	 * Converts form decorators to an array
	 *
	 * @return array
	 */
	private function convertFormDecorators()
	{
		$decorators = $this->getDecorators();//$element->getDecorators();
		$retVal = array();
		foreach($decorators as $key =>$decorator){
			$decorName = str_replace('Zend_Form_Decorator_','', get_class($decorator));
			//$decorOptions = $decorator->getOptions();
			$keyName = lcfirst(str_replace('Zend_Form_Decorator_','', $key));
			$retVal[$keyName] = array('decorator'=>$decorName);
		}
		return $retVal;

	}

	/**
	 * Converts form filters to an array
	 *
	 * @param $element Zend_Form_Element
	 * @return array
	 */
	private function convertFilters($element)
	{
		$retVal = array();
		$filters = $element->getFilters();
		if($filters) {
			$arrayElement['options']['filters'] = array();
			foreach ($filters as $filterKey => $filter) {
				$retVal[$this->getShortName($filter)]  = array('filter' => $this->getShortName($filter));
			}
		}
		return $retVal;
	}

	/**
	 * Reads the Display groups and returns an array containing them.
	 *
	 * @return array
	 */
	/*
	private function convertDisplayGroups()
	{
		$displayGroups = $this->getDisplayGroups();
		$retVal = array();
		if(!empty($displayGroups)) {

			foreach($displayGroups as $key=>$displayGroup)
			{
				$elements = $displayGroup->getElements();

				foreach($elements as $ekey => $value)
				{
					$retVal[$key]['elements'][$ekey] = $ekey;
				}
			}
		}
		return $retVal;
	}
	*/
	/**
	 * Converts form validators to an array
	 *
	 * @param $element Zend_Form_Element
	 * @return array
	 */
	private function convertValidators($element)
	{
		$validators = $element->getValidators();
		$retVal = array();
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
				$retVal[$array_validator['validator']] = $array_validator;
			}
		}
		return $retVal;
	}
}