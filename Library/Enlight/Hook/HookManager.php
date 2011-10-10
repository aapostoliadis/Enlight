<?php
class Enlight_Hook_HookManager extends Enlight_Class
{	
    protected $proxy_factory = null;
	protected $list = array();
	protected $aliases = array();
    
	public function registerHook(Enlight_Hook_HookHandler $handler)
	{
		$list =& $this->list[$handler->getClass()][$handler->getMethod()][$handler->getType()];
		
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
	
	public function addSubscriber(Enlight_Hook_HookSubscriber $subscriber)
	{
		$hooks = $subscriber->getSubscribedHooks();
		if(!empty($hooks))
		foreach ($hooks as $hook)
		{
			$this->registerHook($hook);
		}
		
		return $this;
	}
	
	public function hasHooks($class, $method=null)
	{
		$class = is_object($class) ? get_class($class) : $class;
		$class = isset($this->aliases[$class]) ? $this->aliases[$class] : $class;
		if(isset($method))
		{
			return isset($this->list[$class][$method]);
		}
		else
		{
			return isset($this->list[$class]);
		}
	}
	
	public function getHooks($class, $method=null, $type=null)
	{
		$class = is_object($class) ? get_class($class) : $class;
		$class = isset($this->aliases[$class]) ? $this->aliases[$class] : $class;
        if(isset($type))
            return isset($this->list[$class][$method][$type]) ? $this->list[$class][$method][$type] : array();
        elseif(isset($method))
            return isset($this->list[$class][$method]) ? $this->list[$class][$method] : array();
		else
			return isset($this->list[$class]) ? $this->list[$class] : array();
	}
    
    public function getProxy($class)
    {
        if(!$this->proxy_factory) {
            $this->proxy_factory = new Enlight_Hook_ProxyFactory();
        }
        return $this->proxy_factory->getProxy($class);
    }
    
    public function hasProxy($class)
    {
    	if(!$this->proxy_factory) {
            $this->proxy_factory = new Enlight_Hook_ProxyFactory();
        }
        return $this->proxy_factory->getProxy($class)!==$class;
    }
    
    public function executeHooks(Enlight_Hook_HookArgs $args)
	{
        $hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeBefore);
        if($hooks) {
	        foreach ($hooks as $hook) {
	    		$hook->execute($args);
	    	}
        }
    	
    	$hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeReplace);
        if($hooks) {
	        foreach ($hooks as $hook) {
	    		$args->setReturn($hook->execute($args));
	    	}
        } elseif(method_exists($args->getSubject(), 'executeParent')) {
        	$args->setReturn($args->getSubject()->executeParent($args->getMethod(), $args->getArgs()));
        } elseif(method_exists($args->getSubject(), 'excuteParent')) {
        	$args->setReturn($args->getSubject()->excuteParent($args->getMethod(), $args->getArgs()));
        }
    	
        $hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeAfter);
        if($hooks) {
	        foreach ($hooks as $hook) {
	    		$hook->execute($args);
	    	}
        }
        
    	return $args->getReturn();
    }
	
	public function setAlias($name, $target)
    {
        $this->aliases[$target] = $name;
        return $this;
    }
	
	public function getAlias($name)
    {
        return isset($this->_aliases[$name]) ? $this->_aliases[$name] : null;
    }
    
    public function resetHooks()
	{
		$this->list = array();
		$this->aliases = array();
		return $this;
	}
}