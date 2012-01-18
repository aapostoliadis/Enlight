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
class Enlight_Controller_Router_Default extends Enlight_Controller_Router
{
    /**
     * @var Enlight_Controller_Front
     */
    protected $front;

    /**
     * @var string
     */
    protected $separator = '/';

    /**
     * @throws Enlight_Controller_Exception
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Request_Abstract|Zend_Controller_Request_Http
     */
    public function route(Zend_Controller_Request_Abstract $request)
    {
        if ($event = Enlight_Application::Instance()->Events()->notifyUntil('Enlight_Controller_Router_Route', array(
           'subject' => $this,
           'request' => $request
        ))
        ) {
            $params = $event->getReturn();
        } elseif ($request instanceof Enlight_Controller_Request_RequestHttp) {
            /** @var $request Enlight_Controller_Request_RequestHttp */
            $params = $this->routeDefault($request);
        } else {
            throw new Enlight_Controller_Exception('No route matched the request', Enlight_Controller_Exception::NO_ROUTE);
        }

        $params = Enlight_Application::Instance()->Events()->filter('Enlight_Controller_Router_FilterRouteParams', $params);

        $request->setParams($params);

        return $request;
    }

    /**
     * @param Enlight_Controller_Request_RequestHttp $request
     * @return array
     */
    public function routeDefault(Enlight_Controller_Request_RequestHttp $request)
    {
        $path = $request->getPathInfo();

        if (empty($path)) {
            return array();
        }

        $dispatcher = $this->front->Dispatcher();

        $query = array();
        $params = array();
        foreach (explode($this->separator, trim($path, $this->separator)) as $routePart) {
            $routePart = urldecode($routePart);
            if (empty($query[$request->getModuleKey()]) && $dispatcher->isValidModule($routePart)) {
                $query[$request->getModuleKey()] = $routePart;
            } elseif (empty($query[$request->getControllerKey()])) {
                $query[$request->getControllerKey()] = $routePart;
            } elseif (empty($query[$request->getActionKey()])) {
                $query[$request->getActionKey()] = $routePart;
            } else {
                $params[] = $routePart;
            }
        }

        if ($params) {
            $chunks = array_chunk($params, 2, false);
            foreach ($chunks as $chunk) {
                if (isset($chunk[1])) {
                    $query[$chunk[0]] = $chunk[1];
                } else {
                    $query[$chunk[0]] = '';
                }
            }
        }

        return $query;
    }

    /**
     * @param array $userParams
     * @return mixed|string
     */
    public function assemble($userParams = array())
    {
        if (is_string($userParams)) {
            $userParams = parse_url($userParams, PHP_URL_QUERY);
            parse_str($userParams, $userParams);
        }

        $request = $this->front->Request();

        Enlight_Application::Instance()->Events()->notify(
            'Enlight_Controller_Router_PreAssemble',
            array('subject' => $this, 'request' => $request)
        );

        $params = array_merge($this->globalParams, $userParams);

        $params = Enlight_Application::Instance()->Events()->filter(
            'Enlight_Controller_Router_FilterAssembleParams',
            $params,
            array('subject' => $this, 'request' => $request)
        );

        if ($event = Enlight_Application::Instance()->Events()->notifyUntil(
                        'Enlight_Controller_Router_Assemble',
                        array('subject' => $this, 'params' => $params, 'userParams' => $userParams))
                    ) {
            $url = $event->getReturn();
        } else {
            $url = $this->assembleDefault($params);
        }

        $url = Enlight_Application::Instance()->Events()->filter(
                'Enlight_Controller_Router_FilterUrl',
                $url, array(
                    'subject' => $this,
                    'params' => $params,
                    'userParams' => $userParams
                )
        );

        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($request->getBaseUrl(), '/') . '/' . $url;
        }

        return $url;
    }

    /**
     * @param array $params
     * @return string
     */
    public function assembleDefault($params = array())
    {
        $request = $this->front->Request();
        $dispatcher = $this->front->Dispatcher();

        $route = array();

        $module = isset($params[$request->getModuleKey()]) ? $params[$request->getModuleKey()] : $dispatcher->getDefaultModule();
        $controller = isset($params[$request->getControllerKey()]) ? $params[$request->getControllerKey()] : $dispatcher->getDefaultControllerName();
        $action = isset($params[$request->getActionKey()]) ? $params[$request->getActionKey()] : $dispatcher->getDefaultAction();

        unset($params[$request->getModuleKey()], $params[$request->getControllerKey()], $params[$request->getActionKey()]);

        if ($module != $dispatcher->getDefaultModule()) {
            $route[] = $module;
        }
        if (count($params) > 0 || $controller != 'index' || $action != 'index') {
            $route[] = $controller;
        }
        if (count($params) > 0 || $action != 'index') {
            $route[] = $action;
        }

        foreach ($params as $key => $value) {
            $route[] = $key;
            $route[] = $value;
        }

        $route = array_map('urlencode', $route);
        return implode($this->separator, $route);
    }
}