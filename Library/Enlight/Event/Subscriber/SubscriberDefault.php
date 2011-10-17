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
class Enlight_Event_Subscriber_SubscriberDefault extends Enlight_Event_Subscriber_Subscriber
{
    /**
     * @var array
     */
    protected $listeners;

    /**
     * @var Enlight_Config
     */
    protected $storage;

    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        if(is_string($options)) {
            $options = array('storage' => $options);
        }
        if(!isset($options['storage'])) {
            $options['storage'] = 'event-subscriber';
        }
        if(is_string($options['storage'])) {
            $this->storage = new Enlight_Config($options['storage'], array(
                'allowModifications' => true,
                'adapter' => isset($options['storageAdapter']) ? $options['storageAdapter'] : null,
                'section' => isset($options['section']) ? $options['section'] : null
            ));
        } elseif($options['storage'] instanceof Enlight_Config) {
            $this->storage = $options['storage'];
        } else {
            throw new Enlight_Event_Exception('');
        }
    }

    /**
     * Retrieve a list of listeners registered.
     *
     * @return  array
     */
    public function getListeners()
    {
        if($this->listeners === null) {
            $this->loadListeners();
        }
        return $this->listeners;
    }

    /**
     * Register a listener to an event.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_Subscriber_Subscriber
     */
    public function registerListener(Enlight_Event_EventHandler $handler)
    {
        $this->listeners[] = $handler;
        $this->storage[] = $handler->toArray();
        $this->storage->write();
        return $this;
    }

    /**
     * Remove an event listener from storage.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_Subscriber_Subscriber
     */
    public function removeListener(Enlight_Event_EventHandler $handler)
    {
        $this->listeners = array_diff($this->listeners, array($handler));
        return $this;
    }

    /**
     * Loads the event listener from storage.
     */
    protected function loadListeners()
    {
        $this->listeners = array();
        foreach($this->storage as $entry) {
            if(!$entry instanceof Enlight_Config) {
                continue;
            }
            $this->listeners[] = new Enlight_Event_EventHandler(
                $entry->name,
                $entry->listener,
                $entry->position
            );
        }
    }
}