<?php
class Enlight_Plugin_PluginManager extends Enlight_Class implements Countable, IteratorAggregate
{	
	protected $_defaultNamespaceClass = 'Enlight_Plugin_Namespace';
	protected $list = array();
    
    public function getNamespace($namespace)
    {
    	if(!isset($this->list[$namespace])) $this->loadNamespace($namespace);
		return isset($this->list[$namespace]) ? $this->list[$namespace] : null;
    }
    
    public function getPlugin($namespace, $plugin)
    {
    	return $this->getNamespace($namespace)->getPlugin($plugin);
    }
    
    public function registerNamespace(Enlight_Plugin_Namespace $namespace)
    {
    	$this->list[$namespace->getName()] = $namespace;
    }
    
    public function loadNamespace($namespace)
    {
    	if(!isset($this->list[$namespace])) {
    		$this->list[$namespace] = new $this->_defaultNamespaceClass($this, $namespace);
    	}  
    	return $this;
    }

	public function getList()
    {
    	return $this->list;
    }
    
    public function count()
    {
    	return count($this->list);
    }
    
    public function getIterator()
    {
    	return new ArrayObject($this->list);
    }
    
    public function __call ($name, $arguments=null)
    {
    	return $this->getNamespace($name);
    }
    
    public function resetPlugins()
	{
		$this->list = array();
		return $this;
	}
}