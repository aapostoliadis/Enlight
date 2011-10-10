<?php
class Enlight_Hook_HookArgs extends Enlight_Collection_ArrayCollection
{
    protected $_class;
    protected $_method;
    protected $_name;
	protected $_return;
	
	public function __construct ($class, $method, array $args=null)
	{
		$this->_name = get_parent_class($class);
		$this->_class = $class;
		$this->_method = $method;
		parent::__construct($args);
	}
	public function getSubject()
	{
		return $this->_class;
	}
	public function getMethod()
	{
		return $this->_method;
	}
    public function getArgs()
	{
        return array_values($this->_elements);
	}
    public function getName()
	{
		return $this->_name;
	}
	public function setReturn($return)
	{
		$this->_return = $return;
	}
	public function getReturn()
	{
		return $this->_return;
	}
	
	public function remove($key)
    {
        $this->set($key, null);
    }
    public function set($key, $value)
    {
    	if($this->containsKey($key))
    	{
    		parent::set($key, $value);
    	}
    }
}