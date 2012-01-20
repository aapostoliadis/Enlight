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
 * The Enlight_Extensions_Log_Bootstrap sets the log resource available.
 * It supports various writer, for example firebug, database tables and log files.
 * In additionally the Enlight_Extensions_Log_Bootstrap support to log the ip and the user agents.
 *
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
     * @var Enlight_Components_Log
     */
    protected $log;

    /**
     * Install log plugin
     * @return bool
     */
    public function install()
    {
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_Log', 'onInitResourceLog');

        $this->subscribeEvent('Enlight_Controller_Front_RouteStartup', 'onRouteStartup');

        $this->subscribeEvent('Enlight_Controller_Front_DispatchLoopShutdown', 'onDispatchLoopShutdown', 500);
        /*array(
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
        array(
            'writerName' => 'Mail',
            'writerParams' => array(
                //'mail' => '',
                'from' => 'info@shopware.de',
                'to' => 'info@shopware.de',
                'subjectPrependText' => 'Fehler: '
            ),
            'filterName' => 'Priority',
            'filterParams' => array(
                'priority' => Enlight_Components_Log::WARN
            )
        )*/
        return true;
    }

    /**
     * @param Enlight_Components_Log|Zend_Log $log
     */
    public function setResource(Zend_Log $log = null)
    {
        if($log === null) {
            $config = $this->Config();
            if(count($config) === 0) {
               $config = new Enlight_Config(array(
                   array('writerName' => 'Null'),
                   array('writerName' => 'Firebug')
               ));
            }
            $log = Enlight_Components_Log::factory($config);
        }
        $this->log = $log;
    }

    /**
     * @param Zend_Wildfire_Channel_HttpHeaders $channel
     */
    public function setFirebugChannel($channel = null)
    {
        if($channel === null) {
            $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
        }
        $this->channel = $channel;
    }

    /**
     * @return Enlight_Components_Log
     */
    public function Resource()
    {
        if($this->log === null) {
            $this->setResource();
        }
        return $this->log;
    }

    /**
     * @return Zend_Wildfire_Channel_HttpHeaders
     */
    public function FirebugChannel()
    {
        if($this->channel === null) {
            $this->setFirebugChannel();
        }
        return $this->channel;
    }

    /**
     * Resource handler for log plugin
     *
     * @param   Enlight_Event_EventArgs $args
     * @return  Enlight_Components_Log
     */
    public function onInitResourceLog(Enlight_Event_EventArgs $args)
    {
        return $this->Resource();
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
        $log = $this->Resource();

        $log->setEventItem('remote_address', $request->getClientIp(false));
        $log->setEventItem('user_agent', $request->getHeader('USER_AGENT'));

        $channel = $this->FirebugChannel();
        $channel->setRequest($request);
        $channel->setResponse($response);
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
