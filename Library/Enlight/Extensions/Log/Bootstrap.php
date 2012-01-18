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
class Enlight_Extensions_Log_Bootstrap extends Enlight_Plugin_Bootstrap_Config
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
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_Log', null, 'onInitResourceLog');

        $this->subscribeEvent('Enlight_Controller_Front_RouteStartup', null, 'onRouteStartup');

        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopShutdown', 500, 'onDispatchLoopShutdown');
    }

    /**
     * Resource handler for log plugin
     *
     * @param Enlight_Event_EventArgs $args
     * @return Zend_Log
     */
    public function onInitResourceLog(Enlight_Event_EventArgs $args)
    {
        return Enlight_Components_Log::factory(array(
            array('writerName' => 'Null'),
            array('writerName' => 'Firebug'),/*
            array(
                'writerName' => 'Db',
                'writerParams' => array(
                    'table' => 'log',
                    'db' => $this->Application()->Db(),
                    'columnMap' => array(
                        'priority'       => 'priorityName',
                        'message'        => 'message',
                        'date'           => 'timestamp',
                        'remote_address' => 'remote_address',
                        'user_agent'     => 'user_agent',
                    )
                ),
                'filterName' => 'Priority',
                'filterParams' => array(
                    'priority' => Enlight_Components_Log::ERR
                )
            )
            /*
            array(
                'writerName' => 'Mail',
                'writerParams' => array(
                    //'mail' => '',
                    'from' => 'hl@shopware.de',
                    'to' => 'hl@shopware.de',
                    'subjectPrependText' => 'Fehler: '
                ),
                'filterName' => 'Priority',
                'filterParams' => array(
                    'priority' => Enlight_Components_Log::WARN
                )
            )*/));
    }

    /**
     * On Route add user-agent and remote-address to log component
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onRouteStartup(Enlight_Event_EventArgs $args)
    {
        /** @var $request Enlight_Controller_Request_RequestHttp */
        $request = $args->getSubject()->Request();
        /** @var $request Enlight_Controller_Request_ResponseHttp */
        $response = $args->getSubject()->Response();

        /** @var $log Zend_Log */
        $log = $this->Application()->Log();

        $log->setEventItem('remote_address', $request->getClientIp(false));
        $log->setEventItem('user_agent', $request->getServer('HTTP_USER_AGENT'));

        $this->channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        $this->channel->setRequest($request);
        $this->channel->setResponse($response);
    }

    /**
     * On Dispatch Shutdown collect sql performance results and dump to log component
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onDispatchLoopShutdown(Enlight_Event_EventArgs $args)
    {
        if ($this->channel !== null) {
            $this->channel->flush();
        }
    }
}
