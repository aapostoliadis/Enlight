<?php
class Enlight_Event_EventManager extends Enlight_Class
{
	protected $listeners = array();

	public function registerListener(Enlight_Event_EventHandler $handler)
	{
		$list =& $this->listeners[$handler->getName()];
				
		if($handler->getPosition())
			$position = (int) $handler->getPosition();
		else
			$position = count($list);
			
        while (isset($list[$position]))
        {
            ++$position;
        }
        
        $list[$position] = $handler;
        
        ksort($list);

        return $this;
	}
	
	public function removeListener(Enlight_Event_EventHandler $handler)
	{
		if(!empty($this->listeners[$handler->getName()]))
		foreach ($this->listeners[$handler->getName()] as $i => $callable) {
			if ($handler->getListener() === $callable->getListener())
			{
				unset($this->listeners[$handler->getName()][$i]);
			}
		}
	}

	public function hasListeners($event)
	{
		return isset($this->listeners[$event]);
	}
	
	public function getListeners($event)
	{
		if(isset($this->listeners[$event]))
			return $this->listeners[$event];
		else
			return array();
	}
	
	public function getEvents()
	{
		return array_keys($this->listeners);
	}

	public function notify($event, $eventArgs = null)
	{
		if(!$this->hasListeners($event)) return;
		if(isset($eventArgs)&&is_array($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
		elseif(!isset($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event);
		elseif(!$eventArgs instanceof Enlight_Event_EventArgs)
			throw new Enlight_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
		$eventArgs->setName($event);
		$eventArgs->setProcessed(false);
		foreach ($this->getListeners($event) as $listener)
		{
			$listener->execute($eventArgs);
		}
		$eventArgs->setProcessed(true);
		return $eventArgs;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $event
	 * @param EventArgs|array $eventArgs
	 * @return EventArgs
	 */
	public function notifyUntil($event, $eventArgs = null)
	{
		if(!$this->hasListeners($event)) return;
		if(isset($eventArgs)&&is_array($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
		elseif(!isset($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event);
		elseif(!$eventArgs instanceof Enlight_Event_EventArgs)
			throw new Enlight_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
		$eventArgs->setName($event);
		$eventArgs->setProcessed(false);
		foreach ($this->getListeners($eventArgs->getName()) as $listener)
		{
			if (null !== ($return = $listener->execute($eventArgs)) || $eventArgs->isProcessed())
			{
				$eventArgs->setProcessed(true);
				$eventArgs->setReturn($return);
			}
			if($eventArgs->isProcessed())
			{
				return $eventArgs;
			}
		}
	}
	
	public function filter($event, $value, $eventArgs = null)
	{
		if(!$this->hasListeners($event)) return $value;
		if(isset($eventArgs)&&is_array($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
		elseif(!isset($eventArgs))
			$eventArgs = new Enlight_Event_EventArgs($event);
		elseif(!$eventArgs instanceof Enlight_Event_EventArgs)
			throw new Enlight_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
		$eventArgs->setReturn($value);
		$eventArgs->setName($event);
		$eventArgs->setProcessed(false);
		foreach ($this->getListeners($event) as $listener)
		{
			$eventArgs->setReturn($listener->execute($eventArgs));
		}
		$eventArgs->setProcessed(true);
		return $eventArgs->getReturn();
	}
	
	public function addSubscriber(Enlight_Event_EventSubscriber $subscriber)
	{
		$listeners = $subscriber->getSubscribedEvents();
		if(!empty($listeners))
		foreach ($listeners as $listener)
		{
			$this->registerListener($listener);
		}
	}
	
	public function resetEvents()
	{
		$this->listeners = array();
		return $this;
	}
}