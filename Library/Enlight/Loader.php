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
 * @package    Enlight_Loader
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Loader
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Loader extends Enlight_Class
{
    const DEFAULT_SEPARATOR = '_\\';
	const DEFAULT_EXTENSION = '.php';

	const POSITION_APPEND = 'append';
	const POSITION_PREPEND = 'prepend';
	const POSITION_REMOVE = 'remove';

    /**
     * @var array
     */
	protected $namespaces = array();

    /**
     * @var array
     */
	protected $loadedClasses = array();

	/**
	 * Init loader method
	 */
	public function init()
	{
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			spl_autoload_register(array($this, 'autoload'), true, true);
		} else {
			spl_autoload_register(array($this, 'autoload'), true);
		}
	}
	
	/**
	 * Loads a class by name.
	 *
	 * @param   string|array $class
	 * @param   string $path
	 * @return  bool
	 */
	public function loadClass($class, $path=null)
	{
		if(is_array($class)) {
			return min(array_map(array($this, __METHOD__), $class));
		}
		if(!is_string($class)) {
    		throw new Enlight_Exception('Class name must be a string');
    	}
		if(!$this->isLoaded($class)) {
			if(!$path) {
				$path = $this->getClassPath($class);
			}
			if($path) {
				$this->loadFile($path);
			}
		}
		if(!$this->isLoaded($class)) {
			return false;
		}
		
		$this->loadedClasses[] = $class;
		return true;
	}
	
	/**
	 * Loads file method
	 *
	 * @param   string $path
	 * @return  mixed
	 */
	public static function loadFile($path)
	{
        if(!self::checkFile($path)) {
			throw new Enlight_Exception('Security check: Illegal character in filename');
		}
		if(!self::isReadable($path)) {
			throw new Enlight_Exception('File "'.$path.'" not exists failure');
		}
		if(!ob_start()) {
			throw new Enlight_Exception('Output buffering could not be started');
		}
		
		$result = include $path;
		ob_end_clean();
		
		return $result;
	}
	
	/**
	 * Checks the file is readable
	 *
	 * @param   string $path
	 * @return  string|bool
	 */
	public static function isReadable($path)
	{
		if (is_readable($path)) {
            return $path;
        }
        
        if(function_exists('stream_resolve_include_path')) {
        	$path = stream_resolve_include_path($path);
        	if($path !== false && is_readable($path)) {
        		return $path;
        	} else {
        		return false;
        	}
        }
                
        if (empty($path) 
          || $path{0} === '/'
          || $path{0} === DIRECTORY_SEPARATOR) {
        	return false;
        }
        
        foreach (self::explodeIncludePath() as $includePath) {
            if ($includePath == '.') {
                continue;
            }
            $file = realpath($includePath . '/' . $path);
            if ($file && is_readable($file)) {
                return $file;
            }
        }
        
		return false;
	}
	
	/**
	 * Explode include path 
	 *
	 * @param   string $path
	 * @return  array
	 */
	public static function explodeIncludePath($path = null)
    {
        if (null === $path) {
            $path = get_include_path();
        }

        if (PATH_SEPARATOR == ':') {
            // On *nix systems, include_paths which include paths with a stream 
            // schema cannot be safely explode'd, so we have to be a bit more
            // intelligent in the approach.
            $paths = preg_split('#:(?!//)#', $path);
        } else {
            $paths = explode(PATH_SEPARATOR, $path);
        }
        return $paths;
    }

    /**
     * Returns class path
     *
     * @param   string $class
     * @return  string|void
     */
	public function getClassPath($class)
	{
		foreach ($this->namespaces as  $namespace) {
			if(strpos($class, $namespace['namespace'])!==0) {
				continue;
			}
			$path = substr($class, strlen($namespace['namespace'])+1);
			$path = str_replace(str_split($namespace['separator']), DIRECTORY_SEPARATOR, $path);
			$path = $namespace['path'].$path.$namespace['extension'];
			$path = self::isReadable($path);
			if($path) {
				return $path;
			}
		}
        return null;
	}
	
	/**
	 * Check class is loaded
	 *
	 * @param   string $class
	 * @return  bool
	 */
	public static function isLoaded($class)
	{
		return class_exists($class, false) || interface_exists($class, false);
	}
	
	/**
	 * Register namespace
	 *
	 * @param   string $namespace
	 * @param   string $path
	 * @param   string $separator
	 * @param   string $extension
	 * @param   string $position
     * @return  Enlight_Loader
     */
	public function registerNamespace($namespace, $path,
	  $separator=self::DEFAULT_SEPARATOR, 
	  $extension=self::DEFAULT_EXTENSION,
	  $position=self::POSITION_APPEND)
	{
		$namespace = array(
			'namespace' => $namespace,
			'path' => $path,
			'separator' => $separator,
			'extension' => $extension
		);
		
		switch ($position) {
			case self::POSITION_APPEND:
				array_push($this->namespaces, $namespace);
				break;
			case self::POSITION_PREPEND:
				array_unshift($this->namespaces, $namespace);
				break;
			default:
				break;
		}

        return $this;
	}
	
	/**
	 * Adds a path to the include paths
     * Returns the old include paths
	 *
	 * @param   string $path
	 * @param   string $position
	 * @return  string
	 */
	public static function addIncludePath($path, $position=self::POSITION_APPEND)
	{
		if(is_array($path)) {
			return (bool) array_map(__METHOD__, $path);
		}
		if(!is_string($path) || !file_exists($path) || !is_dir($path)) {
			throw new Enlight_Exception('Path "'.$path.'" is not a dir failure');
		}

		$paths = self::explodeIncludePath();

		if (($key = array_search($path, $paths)) !== false) {
			unset($paths[$key]);
		}
		
		switch ($position) {
			case self::POSITION_APPEND:
				array_push($paths, $path);
				break;
			case self::POSITION_PREPEND:
				array_unshift($paths, $path);
				break;
			default:
				break;
		}
		
		return self::setIncludePath($paths);
	}
	
	/**
	 * Sets the include path
     * Returns the old include paths 
	 *
	 * @param   string|array $path
	 * @return  string
	 */
	public static function setIncludePath($path)
	{
		if(is_array($path)) {
			$path = implode(PATH_SEPARATOR, $path);
		}
		       
        $old = set_include_path($path);
        if($old !== $path 
          && (!$old || $old == get_include_path())) {
        	throw new Enlight_Exception('Include path "'.$path.'" could not be set failure');
        }
        
        return $old;
	}
	
	/**
	 * Returns loaded classes
	 *
	 * @return  array
	 */
	public function getLoadedClasses()
	{
		return $this->loadedClasses;
	}
	
	/**
	 * Callback for auto loading of classes.
	 *
	 * @param   string $class
	 */
	public function autoload($class)
	{
		try {
			$this->loadClass($class);
		}
		catch (Exception $e) { }
	}
	
	/**
	 * Security check file
	 *
	 * @param   string $path
	 * @return  bool
	 */
	public static function checkFile($path)
    {
        return !preg_match('/[^a-z0-9\\/\\\\_. :-]/i', $path);
    }
}