<?php
/**
 * HTTP response object for use with Enlight_Controller 
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Controller
 */
class Enlight_Controller_Response_ResponseTestCase extends Zend_Controller_Response_HttpTestCase implements Enlight_Controller_Response_Response
{
	protected $_cookies = array();
	
	/**
	 * Set a cookie method
	 *
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 * @param bool $httpOnly
	 * @return unknown
	 */
    public function setCookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = false)
    {
    	$this->_cookies[$name] = array(
    		'value' => $value,
    		'expire' => $expire,
    		'path' => $path,
    		'domain' => $domain,
    		'secure' => $secure,
    		'httpOnly' => $httpOnly
    	);
        return $this;
    }
    
    /**
     * Get a cookie value
     *
     * @param string $name
     * @param string $default
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function getCookie($name, $default = null)
    {
       return isset($this->_cookies[$name]['value']) ? $this->_cookies[$name]['value'] : $default;
    }
    
    /**
     * Get a header value
     *
     * @param string $name
     * @param string $default
     * @return Zend_Controller_Request_HttpTestCase
     */
    public function getHeader($name, $default = null)
    {
    	foreach ($this->_headers as $header) {
    		if(isset($header['name']) && $header['name'] === $name) {
    			return $header['value'];
    		}
    	}
    	return $default;
    }
}