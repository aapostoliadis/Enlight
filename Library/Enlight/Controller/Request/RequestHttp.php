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
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * HTTP request controller for use with Enlight_Controller.
 *
 * The Enlight_Controller_Request_RequestHttp represents the request object (what's in the url, which was read out).
 *
 * @category   Enlight
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Controller_Request_RequestHttp
    extends Zend_Controller_Request_Http
    implements Enlight_Controller_Request_Request
{
    /**
     * Set GET values method
     *
     * @param  string|array $spec
     * @param  null|mixed   $value
     * @return Zend_Controller_Request_Http
     */
    public function setQuery($spec, $value = null)
    {
        if (!is_array($spec) && $value === null) {
            unset($_GET[$spec]);
            return $this;
        }
        return parent::setQuery($spec, $value);
    }

    /**
     * Sets the request URI scheme
     *
     * @param $value
     * @return Enlight_Controller_Request_RequestHttp
     */
    public function setSecure($value = true)
    {
        $_SERVER['HTTPS'] = $value ? 'on' : null;
        return $this;
    }

    /**
     * Set HTTP host method
     *
     * @param string $host
     * @return Enlight_Controller_Request_RequestHttp
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
        if ($this->_requestUri === null
                && !empty($_SERVER['argc'])
                && $_SERVER['argc'] > 1) {
            $this->setRequestUri($_SERVER['argv'][1]);
        }
        return $this;
    }
}