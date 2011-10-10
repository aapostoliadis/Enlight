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
class Enlight_Controller_Request_RequestHttp extends Zend_Controller_Request_Http implements Enlight_Controller_Request_Request
{
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
     */
    public function setHttpHost($host)
    {
    	$_SERVER['HTTP_HOST'] = $host;
    	return $this;
    }
    
    /**
     * Set the REQUEST_URI on which the instance operates
     *
     * If no request URI is passed, uses the value in $_SERVER['REQUEST_URI'],
     * $_SERVER['HTTP_X_REWRITE_URL'], or $_SERVER['ORIG_PATH_INFO'] + $_SERVER['QUERY_STRING'].
     *
     * @param string $requestUri
     * @return Zend_Controller_Request_Http
     */
    public function setRequestUri($requestUri = null)
    {
    	parent::setRequestUri($requestUri);
    	if($this->_requestUri === null
    	  && !empty($_SERVER['argc'])
    	  && $_SERVER['argc'] > 1) {
    	  	$this->setRequestUri($_SERVER['argv'][1]);
    	}
    	return $this;
    }
}