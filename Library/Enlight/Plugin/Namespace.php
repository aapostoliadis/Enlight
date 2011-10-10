<?php
class Enlight_Plugin_Namespace extends Enlight_Class implements Countable, IteratorAggregate
{
	protected $name;
	protected $path = array();
	protected $list = array();
	
	public function __construct($manager, $name)
	{
		$this->name = $name;
		parent::__construct();
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function hasPlugin($plugin)
	{
		return isset($this->list[$plugin]);
	}
	
	public function getPlugin($plugin)
	{
		if(!isset($this->list[$plugin])) $this->loadPlugin($plugin);
		return isset($this->list[$plugin]) ? $this->list[$plugin] : null;
	}
	
	public function loadPlugin($plugin)
	{
		if(isset($this->list[$plugin])) {
			return true;
		}
		foreach ($this->path as $path=>$prefix)
		{
			$file = $path . $plugin . Enlight_Application::DS() . 'Bootstrap.php';
			if(!file_exists($file)){
				continue;
			}
			$class = $prefix.'_'.$plugin.'_Bootstrap';
			Enlight_Application::Instance()->Loader()->loadClass($class, $file);
			$this->list[$plugin] = Enlight_Class::Instance($class, array($this, $plugin));
			return true;
		}
		return false;
	}
	
	public function addPrefixPath($prefix, $path)
	{
		if(!file_exists($path)||!is_dir($path)) {
			throw new Enlight_Exception('Parameter path "'.$path.'" is not a valid directory failure');
		}
		$prefix = trim($prefix, '_');
		$path = realpath($path) . Enlight_Application::DS();
		$this->path[$path] = $prefix;
		return $this;
	}
	
	public function loadAll()
	{
		foreach ($this->path as $path=>$prefix)
		{
			foreach (new DirectoryIterator($path) as $dir)
			{
			    if(!$dir->isDir()||$dir->isDot()){
			    	continue;
			    }
			    $file = $dir->getPathname() . Enlight_Application::DS().'Bootstrap.php';
			    if(!file_exists($file)){
			    	continue;
			    }
			    $plugin = $dir->getFilename();
				$this->loadPlugin($plugin);
			}
		}
		return $this;
	}
	
	public function getList()
	{
		return $this->list;
	}
	
	public function resetPlugins()
    {
        $this->list = array();
        return;
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
    	return $this->getPlugin($name);
    }
}