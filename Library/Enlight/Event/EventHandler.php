<?php
class Enlight_Event_EventHandler
{
    protected $name;
    protected $listener;
    protected $position;
    protected $plugin;
	
	public function __construct ($event, $listener, $position=null, $plugin=null)
	{
		if(empty($event)||empty($listener)) {
			throw new Enlight_Exception('Some parameters are empty');
		}
		if(!is_callable($listener, true, $listener_event)) {
			throw new Enlight_Exception('Listener "'.$listener_event.'" is not callable');
		}
		$this->name = $event;
		$this->listener = $listener;
		$this->setPosition($position);
		$this->setPlugin($plugin);
	}
	
	public function setPosition($position=0)
	{
		if($position!==null&&!is_numeric($position)) {
			throw new Enlight_Exception('Position is not numeric');
		}
		$this->position = (int) $position;
		return $this;
	}
	
	public function setPlugin($plugin=0)
	{
		$this->plugin = $plugin;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function getListener()
	{
		return $this->listener;
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