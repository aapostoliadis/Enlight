<?php
/**
 * Enlight Config
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 */
class Enlight_Config extends Zend_Config implements ArrayAccess
{
	protected $_defaultConfigClass = __CLASS__;
	
	/**
	 * Constructor method
	 *
	 * @param array $array
	 * @param bool $allowModifications
	 */
	public function __construct(array $array, $allowModifications = false)
    {
    	$data = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $data[$key] = new $this->_defaultConfigClass($value, $allowModifications);
            } else {
                $data[$key] = $value;
            }
        }
        parent::__construct($data, $allowModifications);
    }
    
    /**
     * Set value method
     *
     * @param string $name
     * @param mixed $value
     * @return Enlight_Config
     */
    public function set($name, $value=null)
	{
		$this->__set($name, $value);
		return $this;
	}
	
	/**
	 * Set value method
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		if ($this->_allowModifications) {
            if (is_array($value)) {
                $value = new $this->_defaultConfigClass($value, true);
            }
        }
		parent::__set($name, $value);
	}

	/**
	 * Array access method
	 *
	 * @param string $name
	 * @param mixed $value
	 */
    public function offsetSet($name, $value)
    {
    	$this->__set($name, $value);
    }
    
    /**
     * Array access method
     *
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }
    
    /**
     * Array access method 
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
    	$this->__unset($name);
    }
    
    /**
     * Array access method 
     *
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }
    
    /**
     * Set allow modifications
     *
     * @param bool $option
     * @return Enlight_Config
     */
    public function setAllowModifications($option = true)
    {
        $this->_allowModifications = (bool) $option;
        foreach ($this->_data as $key => $value) {
            if ($value instanceof Enlight_Config) {
                $value->setAllowModifications($option);
            }
        }
        return $this;
    }
}