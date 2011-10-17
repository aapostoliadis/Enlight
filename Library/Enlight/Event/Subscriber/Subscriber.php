<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
abstract class Enlight_Event_Subscriber_Subscriber extends Enlight_Class
{
    /**
     * Retrieve a list of listeners registered to a given event.
     *
     * @return  array
     */
    abstract public function getListeners();

    /**
     * Register a listener to an event.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_Subscriber_Subscriber
     */
    abstract public function registerListener(Enlight_Event_EventHandler $handler);

    /**
     * Remove an event listener.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_Subscriber_Subscriber
     */
    abstract public function removeListener(Enlight_Event_EventHandler $handler);
}