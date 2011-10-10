<?php
class Enlight_Hook_HookHandler
{
    protected $class;
    protected $method;
    protected $hook;
    protected $type;
    protected $position;
    protected $plugin;
    
    const TypeReplace = 1;
	const TypeBefore = 2;
	const TypeAfter = 3;
	
	public function __construct ($class, $method, $listener, $type=self::TypeAfter, $position=0, $plugin=null)
	{
		if(empty($class)||empty($method)||empty($listener))
		{
			throw new Enlight_Exception('Some parameters are empty');
		}
		if(!is_callable($listener, true, $listener_name))
		{
			throw new Enlight_Exception('Listener "'.$listener_name.'" is not callable');
		}
		$this->class = $class;
		$this->method = $method;
		$this->listener = $listener;
		$this->setType($type);
		$this->setPosition($position);
		$this->setPlugin($plugin);
	}
	
	public function setType($type)
	{
		if($type===null)
		{
			$type = self::TypeAfter;
		}
		if(!in_array($type, array(
			self::TypeReplace,
			self::TypeBefore,
			self::TypeAfter
		)))
		{
			throw new Enlight_Exception('Hook type is unknown');
		}
		$this->type = $type;
		return $this;
	}
	
	public function setPosition($position)
	{
		if(!is_numeric($position))
		{
			throw new Enlight_Exception('Position is not numeric');
		}
		$this->position = $position;
		return $this;
	}
	
	public function setPlugin($plugin)
	{
		$this->plugin = $plugin;
		return $this;
	}
	
	public function getName()
	{
		return $this->class.'::'.$this->method;
	}
	
	public function getClass()
	{
		return $this->class;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function getListener()
	{
		return $this->listener;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getPosition()
	{
		return $this->position;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
	
	public function execute($args=null)
	{
		return call_user_func($this->listener, $args);
	}
}