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
class Enlight_Extensions_Site_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
	/**
	 * Install log plugin
	 */
	public function install()
	{
		$this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_Site',
            null,
            'onInitResourceSite'
        );

        $this->subscribeEvent(
            'Enlight_Bootstrap_InitResource_Sites',
            null,
            'onInitResourceSiteManager'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_DispatchLoopStartup',
            null,
            'onStartDispatch'
        );
	}

    /**
     * Resource handler for log plugin

     * @param Enlight_Event_EventArgs $args
     * @return Enlight_Components_Site_Manager
     */
    public function onInitResourceSiteManager(Enlight_Event_EventArgs $args)
    {
        return new Enlight_Components_Site_Manager($this->Config());
    }

	/**
	 * Resource handler for log plugin

	 * @param Enlight_Event_EventArgs $args
	 * @return Zend_Log
	 */
	public function onInitResourceSite(Enlight_Event_EventArgs $args)
	{
        /** @var $log Enlight_Components_Session_Namespace */
	    $session = $this->Application()->Session();

        if(!isset($session->Site)) {
            /** @var $siteManager Enlight_Components_Site_Manager */
            $siteManager = $this->Application()->Sites();
            $session->Site = $siteManager->findOneBy('host', 'test');
            if(!isset($session->Site)) {
                $session->Site = $siteManager->getDefault();
            }
        }

        return $session->Site;
	}

	/**
	 * On Route add user-agent and remote-address to log component
     *
	 * @param Enlight_Event_EventArgs $args
	 */
	public function onStartDispatch(Enlight_Event_EventArgs $args)
	{
        /** @var $request Enlight_Controller_Request_RequestHttp */
		$request = $args->getRequest();

        if(($site = $request->getParam('__site')) !== null) {
            /** @var $siteManager Enlight_Components_Site_Manager */
            $siteManager = $this->Application()->Sites();
            $site = $siteManager->findOneBy('id', $site);

            /** @var $log Enlight_Components_Session_Namespace */
            $session = $this->Application()->Session();
            $session->Site = $session;
        } else {
            /** @var $site Enlight_Components_Site */
            $site = $this->Application()->Site();
        }

        if(($locale = $request->getParam('__locale')) !== null) {
            $site->setLocale($locale);
        }

        if(($currency = $request->getParam('__currency')) !== null) {
            $site->setCurrency($currency);
        }
	}
}
