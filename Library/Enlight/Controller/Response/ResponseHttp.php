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
 * @category   Enlight
 * @package    Enlight_Controller
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Controller_Response_ResponseHttp
    extends Zend_Controller_Response_Http
    implements Enlight_Controller_Response_Response
{
    protected $_cookies = array();

    /**
     * Set a cookie method
     *
     * @param string $name
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     * @return Enlight_Controller_Response_ResponseHttp
     * @link http://www.php.net/manual/de/function.setcookie.php
     */
    public function setCookie($name,
                                $value = null,
                                $expire = 0,
                                $path = null,
                                $domain = null,
                                $secure = false,
                                $httpOnly = false
    )
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
     * Send all headers
     *
     * @return Enlight_Controller_Response_ResponseHttp
     */
    public function sendHeaders()
    {
        if (count($this->_cookies)) {
            $this->canSendHeaders(true);
            foreach ($this->_cookies as $name => $cookie) {
                setcookie(
                    $name,
                    $cookie['value'],
                    $cookie['expire'],
                    $cookie['path'],
                    $cookie['domain'],
                    $cookie['secure'],
                    $cookie['httpOnly']
                );
            }
        }
        return parent::sendHeaders();
    }
}