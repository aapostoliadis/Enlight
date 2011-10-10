<?php
/**
 * Enlight Class
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 */
abstract class Enlight_Class
{
	static protected $instances = array();
	
	/**
	 * Constructor method
	 */
	public function __construct ()
	{
		$class = get_class($this);
		if($this instanceof Enlight_Singleton) {
			if(!isset(self::$instances[$class])) {
				self::$instances[$class] = $this;
			} else {
				throw new Enlight_Exception('Class "'.get_class($this).'" is singleton, please use the instance method');
			}
		}
		if($this instanceof Enlight_Hook
		  && !$this instanceof Enlight_Hook_Proxy
		  && Enlight_Application::Instance()->Hooks()->hasProxy($class)) {
			throw new Enlight_Exception('Class "'.get_class($this).'" has hooks, please use the instance method');
		}
		if(method_exists($this, 'init')) {
            if(func_num_args()) {
            	$arg_list =& func_get_args();
                call_user_func_array(array($this, 'init'), $arg_list);
            } else {
            	$this->init();
            }
        }
	}
	
	/**
	 * Returns the class name
	 *
	 * @param mixed $class
	 * @return string
	 */
	public static function getClassName($class=null)
	{
		if(empty($class)) {
			if(function_exists('get_called_class')) {
				$class = get_called_class();
			} else {
				throw new Enlight_Exception('Method not supported');
			}
		}
		if(is_object($class)) {
			$class = get_class($class);
		} elseif(!class_exists($class)) {
			throw new Enlight_Exception('Class ' . $class . ' does not exist and could not be loaded');
		}
		if(in_array('Enlight_Hook', class_implements($class))) {
    		$class = Enlight_Application::Instance()->Hooks()->getProxy($class);
    	}
    	return $class;
	}

	/**
	 * Returns a instance
	 *
	 * @param string $class
	 * @param array $args
	 * @return Enlight_Class
	 */
	static public function Instance($class=null, $args=null)
	{
		$class = self::getClassName($class);
    	if(isset(self::$instances[$class])) {
			return self::$instances[$class];
		}		
    	
        $rc = new ReflectionClass($class);

        if(isset($args)) {
			$instance = $rc->newInstanceArgs($args);
		} else {
			$instance = $rc->newInstance();
		}
		return $instance;
	}
	
	/**
	 * Reset a instance
	 *
	 * @param mixed $class
	 */
	static public function resetInstance($class=null)
	{
		$class = self::getClassName($class);
		unset(self::$instances[$class]);
	}
	
	/**
	 * Magic caller
	 *
	 * @param string $name
	 * @param array $args
	 */
	public function __call($name, $args=null)
	{
		throw new Enlight_Exception('Method "'.get_class($this).'::'.$name.'" not found failure', Enlight_Exception::Method_Not_Found);
	}
	
	/**
	 * Magic static caller
	 *
	 * @param string $name
	 * @param array $args
	 */
	static public function __callStatic($name, $args=null)
	{
		throw new Enlight_Exception('Method "'.get_called_class().'::'.$name.'" not found failure', Enlight_Exception::Method_Not_Found);
	}
	
	/**
	 * Magic getter
	 *
	 * @param string $name
	 */
	public function __get($name)
	{
		throw new Enlight_Exception('Property "'.$name.'" not found failure', Enlight_Exception::Property_Not_Found);
	}
	
	/**
	 * Magic setter
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value=null)
	{
		throw new Enlight_Exception('Property "'.$name.'" not found failure', Enlight_Exception::Property_Not_Found);
	}
}