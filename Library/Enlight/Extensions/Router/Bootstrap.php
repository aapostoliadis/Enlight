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
 * @category   Enlight
 * @package    Enlight_Extensions
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Extensions_Router_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
    /**
     * @var Zend_Wildfire_Channel_HttpHeaders
     */
    protected $channel;

    /**
     * Install log plugin
     */
    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_Log',
            'onInitResourceLog'
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

     * @param Enlight_Event_EventArgs $args
     * @return Zend_Log
     */
    public function onInitResourceLog(Enlight_Event_EventArgs $args)
    {

    }
}
