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
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Event
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Event_Subscriber_Array extends Enlight_Event_Subscriber
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @param   null|array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options) && isset($options[0])) {
            foreach ($options as $listener) {
                if (!$listener instanceof Enlight_Event_Handler) {
                    $listener = new Enlight_Event_Handler_Default(
                        $listener['event'],
                        $listener['position'],
                        $listener['listener']
                    );
                }
                $this->registerListener($listener);
            }
        }
    }

    /**
     * Retrieve a list of listeners registered.
     *
     * @return  array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * Register a listener to an event.
     *
     * @param   Enlight_Event_Handler $handler
     * @return  Enlight_Event_Subscriber
     */
    public function registerListener(Enlight_Event_Handler $handler)
    {
        $this->listeners[] = $handler;
        return $this;
    }

    /**
     * Remove an event listener from storage.
     *
     * @param   Enlight_Event_Handler $handler
     * @return  Enlight_Event_Subscriber
     */
    public function removeListener(Enlight_Event_Handler $handler)
    {
        $this->listeners = array_diff($this->listeners, array($handler));
        return $this;
    }
}