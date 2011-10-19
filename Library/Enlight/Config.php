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
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Config extends Zend_Config implements ArrayAccess
{
	/**
	 * @var string Default config class
	 */
	protected $_defaultConfigClass = __CLASS__;

    /**
     * Whether in-memory modifications to configuration data are allowed
     *
     * @var boolean
     */
    protected $_allowModifications = false;

	/**
	 * The config name.
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * The dirty fields list.
	 *
	 * @var bool
	 */
	protected $_dirtyFields = array();

	/**
	 * The current section.
	 *
	 * @var string
	 */
	protected $_section;

	/**
	 * The section separator.
	 *
	 * @var string
	 */
	protected $_sectionSeparator = ':';

    /**
     * @var Enlight_Config_Adapter
     */
    protected static $_defaultAdapter;

    /**
     * @var Enlight_Config_Adapter
     */
    protected $_adapter;

    /**
     * Constructor method
     *
     * @param array|null|string $config
     * @param array|bool $options
     * @return \Enlight_Config
     */
	public function __construct($config, $options = null)
    {
        if(!is_array($options)) {
            $options = array('allowModifications' => $options);
        }
        if(isset($options['allowModifications'])) {
            $this->_allowModifications = (bool) $options['allowModifications'];
        }
        if(isset($options['adapter']) && $options['adapter'] instanceof Enlight_Config_Adapter) {
            $this->_adapter = $options['adapter'];
        } else {
            $this->_adapter = self::$_defaultAdapter;
        }
        if(isset($options['section'])) {
            $this->_section = (string) $options['section'];
        }
		if(is_array($config)) {
			$this->setData($config);
		} elseif(!empty($config)) {
			$this->setName($config);
		} else {
			throw new Enlight_Config_Exception('Please specify configuration data');
		}
    }

	/**
	 * Sets the config name.
	 *
	 * @param string $name
	 * @return Enlight_Config
	 */
    protected function setName($name)
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * Returns the config name.
	 * 
	 * @return Enlight_Config
	 */
    public function getName()
	{
		return $this->_name;
	}

	/**
	 * Set value method
	 * @param array $data
	 */
    public function setData(array $data)
	{
		$this->_loadedSection = null;
        $this->_index = 0;
        $this->_data = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->_data[$key] = new $this->_defaultConfigClass($value, $this->_allowModifications);
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_count = count($this->_data);
	}

	/**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
		if($this->_data === null) {
			$this->read();
		}

        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        } else {
			return $default;
		}
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
     * Defined by Iterator interface
     */
    public function rewind()
    {
        if($this->_data === null) {
			$this->read();
		}
        parent::rewind();
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        if($this->_data === null) {
			$this->read();
		}
        return isset($this->_data[$name]);
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
            if($name === null) {
                $this->_data[] = $value;
                $this->_count = count($this->_data);
            } else {
			    $this->_dirtyFields[] = $name;
			    parent::__set($name, $value);
            }
        } else {
            throw new Enlight_Config_Exception('Enlight_Config is read only');
        }
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
        if($this->_data !== null) {
            foreach ($this->_data as $value) {
                if ($value instanceof Enlight_Config) {
                    $value->setAllowModifications($option);
                }
            }
        }
        return $this;
    }

	/**
	 * Resets the dirty fields to an empty list.
	 *
	 * @return Enlight_Config
	 */
	public function resetDirtyFields()
	{
		$this->_dirtyFields = array();
		return $this;
	}

	/**
	 * Returns the dirty field list as an array.
	 *
	 * @param $fields
	 * @return Enlight_Config
	 */
	public function setDirtyFields($fields)
	{
		$this->_dirtyFields = array_unique($fields);
		return $this;
	}

	/**
	 * Returns the dirty field list as an array.
	 *
	 * @return array
	 */
	public function getDirtyFields()
	{
		return $this->_dirtyFields;
	}
	
    /**
     * Sets the current section of the config list.
     *
     * @param string|array $section
     * @return Enlight_Config
     */
    public function setSection($section)
    {
    	if(is_array($section)) {
			$section = implode($this->_sectionSeparator, $section);
		}
		$this->_section = $section;
		return $this;
    }

	/**
     * Returns the current section of the config list.
     *
     * @return string|array
     */
    public function getSection()
    {
		return $this->_section;
    }

	/**
     * Sets an extending section for config adapter.
     *
     * @param string $extendingSection
     * @param string $extendedSection
     * @return Enlight_Config
     */
    public function setExtend($extendingSection, $extendedSection = null)
    {
    	if($extendingSection !== $extendedSection){
    		parent::setExtend($extendingSection, $extendedSection);
    	}
    	return $this;
    }

	/**
     * Sets the extends of the config list.
     *
     * @param array|string $extends
     * @return Enlight_Config
     */
    public function setExtends($extends)
    {
    	if(is_array($extends)) {
    		$extendingSection = $this->_section;
    		foreach ($extends as $key=>$extendedSection) {
    			if(!is_int($key)) {
    				$extendingSection = $key;
    			}
    			if(is_array($extendedSection)) {
    				$extendedSection = implode($this->_sectionSeparator, $extendedSection);
    			}
    			$this->setExtend($extendingSection, $extendedSection);
    		}
    	} else {
    		$this->_assertValidExtend($this->_section, $extends);
    		$this->setExtend($this->_section, $extends);
    	}
    	return $this;
    }

    /**
     * Returns the current config adapter.
     *
     * @return Enlight_Config_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Returns the default config adapter.
     *
     * @return Enlight_Config_Adapter
     */
    public static function getDefaultAdapter()
    {
        return self::$_defaultAdapter;
    }

    /**
     * Sets the default config adapter.
     *
     * @param Enlight_Config_Adapter $adapter
     * @return void
     */
    public static function setDefaultAdapter(Enlight_Config_Adapter $adapter)
    {
        self::$_defaultAdapter = $adapter;
    }

    /**
     * Loads the default data and the sections from the data store.
     *
     * @return Enlight_Config
     */
    public function read()
    {
        $this->_adapter->read($this);
        return $this;
    }

    /**
     * @return Enlight_Config
     */
    public function write()
    {
        $this->_adapter->write($this);
        return $this;
    }
} 