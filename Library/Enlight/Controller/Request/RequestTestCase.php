<?php
/**
 * HTTP request object for use with Enlight_Controller 
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Controller
 */
class Enlight_Controller_Request_RequestTestCase extends Zend_Controller_Request_HttpTestCase implements Enlight_Controller_Request_Request
{
	/**
     * Server params
     * @var array
     */
    protected $_serverParams = array();
    
	/**
     * Set GET values method
     *
     * @param  string|array $spec
     * @param  null|mixed $value
     * @return Zend_Controller_Request_Http
     */
	public function setQuery($spec, $value = null)
    {
    	if(!is_array($spec) && $value===null) {
    		unset($_GET[$spec]);
    		return $this;
    	}
    	return parent::setQuery($spec, $value);
    }
    
    /**
     * Set HTTP host method
     *
     * @param string $host
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setHttpHost($host)
    {
    	$this->setHeader('HOST', $host);
    	return $this;
    }
    
    /**
     * Set HTTP client method
     *
     * @param string $host
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setClientIp($ip, $setProxy = true)
    {
    	if($setProxy) {
    		$this->setHeader('CLIENT_IP', $ip);
    	} else {
    		$this->setServer('REMOTE_ADDR', $ip);
    	}
    	
    	return $this;
    }
    
    /**
     * Set a server param
     *
     * @param  string $key
     * @param  string $value
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setServer($key, $value = null)
    {
        $this->_serverParams[$key] = $value===null ? null : (string) $value;
        return $this;
    }
    
    /**
     * Get a server param
     *
     * @param string $key
     * @param string $default
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge($_SERVER, $this->_serverParams);
        } elseif (isset($this->_serverParams[$key])) {
        	return $this->_serverParams[$key]!==null ? $this->_serverParams[$key] : $default;
        } elseif (isset($_SERVER[$key])) {
        	return $_SERVER[$key];
        } else {
        	return $default;
        }
    }
    
    /**
     * Set a request header
     *
     * @param  string $key
     * @param  string $value
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function setHeader($key, $value = null)
    {
    	if($value !== null) {
    		$key = $this->_normalizeHeaderName($key);
			$this->_headers[$key] = (string) $value;
    	} else {
    		unset($this->_headers[$key]);
    	}        
        $this->setServer('HTTP_'.$key, $value);
        return $this;
    }
}