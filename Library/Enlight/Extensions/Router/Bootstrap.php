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
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Extensions_Router_Bootstrap sets the router resource available.
 *
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Extensions_Router_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{

    /**
     * @var bool
     */
    protected $useModRewrite = false;

    protected $forceSecureControllers = array();

    protected $absolute;

    /**
     * Install log plugin
     */
    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_Router',
            'onInitResourceRouter'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_RouteStartup',
            'onRouteStartup'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopShutdown',
            'onDispatchLoopShutdown',
            500
        );

        //fullPath
        //forceSecure
        //appendSession
        //scheme
    }

    /**
     * Resource handler for log plugin
     *
     * @param Enlight_Event_EventArgs $args
     * @return Zend_Log
     */
    public function onInitResourceRouter(Enlight_Event_EventArgs $args)
    {

    }

    /**
     * Resource handler for log plugin
     *
     * @param Enlight_Event_EventArgs $args
     * @return Zend_Log
     */
    public function onRouteStartup(Enlight_Event_EventArgs $args)
    {
        $aliases = array(
            'controller' => 'sViewport',
            'action' => 'sAction',
        );
        foreach ($aliases as $key=>$aliase)	{
            if (($value = $request->getParam($key))!==null) {
                $request->setParam($aliase, $value);
            }
        }
    }

    /**
     * Resource handler for log plugin
     *
     * @param Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onFilterAssemble(Enlight_Event_EventArgs $args)
    {
        $params = $args->getReturn();
        $request = $args->getRequest();

        unset($params['fullPath'], $params['appendSession'], $params['forceSecure'], $params['session']);

        return $params;
    }

    /**
     * Event listener method
     *
     * @param Enlight_Event_EventArgs $args
     * @return mixed|string
     */
    public function onFilterUrl(Enlight_Event_EventArgs $args)
    {
        $params = $args->getParams();
        $userParams = $args->get('userParams');

        if(!empty($params['module']) && $params['module']!='frontend' && empty($userParams['fullPath'])) {
            return $args->getReturn();
        }

        if(empty(Shopware()->Config()->UseSSL)) {
            $useSSL = false;
        } elseif(!empty($userParams['sUseSSL'])||!empty($userParams['forceSecure'])) {
            $useSSL = true;
        } elseif(!empty($params['sViewport']) &&
          in_array($params['sViewport'], array('account', 'checkout', 'register', 'ticket', 'note'))) {
            $useSSL = true;
        } else {
            $useSSL = false;
        }

        $url = '';

        if(!isset($userParams['fullPath']) || !empty($userParams['fullPath'])) {
            $url = $useSSL ? 'https://' : 'http://';
            if(Shopware()->Bootstrap()->hasResource('Shop')
              && Shopware()->Bootstrap()->hasResource('Front')) {
                $url .= Shopware()->Shop()->getHost().Shopware()->Front()->Request()->getBasePath();
            } else {
                $url .= Shopware()->Config()->BasePath;
            }
            $url .= '/';
        }

        if(empty(Shopware()->Config()->RouterUseModRewrite)
          && (!empty($params['sViewport']) || empty(Shopware()->Config()->RedirectBaseFile))) {
            $url .= Shopware()->Config()->BaseFile;
            $url .= '/';
        }

        $url .= $args->getReturn();

        return $url;
    }

    /**
     * Resource handler for log plugin

     * @param Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args)
    {

    }
}
