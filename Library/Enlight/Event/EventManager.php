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
class Enlight_Event_EventManager extends Enlight_Class
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Register a listener to an event.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_EventManager
     */
    public function registerListener(Enlight_Event_EventHandler $handler)
    {
        $list =& $this->listeners[$handler->getName()];

        if($handler->getPosition()) {
            $position = (int) $handler->getPosition();
        } else {
            $position = count($list);
        }

        while (isset($list[$position])) {
            ++$position;
        }

        $list[$position] = $handler;

        ksort($list);

        return $this;
    }

    /**
     * Remove an event listener.
     *
     * @param   Enlight_Event_EventHandler $handler
     * @return  Enlight_Event_EventManager
     */
    public function removeListener(Enlight_Event_EventHandler $handler)
    {
        if(!empty($this->listeners[$handler->getName()]))
        foreach ($this->listeners[$handler->getName()] as $i => $callable) {
            if ($handler->getListener() === $callable->getListener()) {
                unset($this->listeners[$handler->getName()][$i]);
            }
        }
        return $this;
    }

    /**
     * @param   string $event
     * @return  bool
     */
    public function hasListeners($event)
    {
        return isset($this->listeners[$event]) && count($this->listeners[$event]);
    }

    /**
     * Retrieve a list of listeners registered to a given event.
     *
     * @param   $event
     * @return  array
     */
    public function getListeners($event)
    {
        if(isset($this->listeners[$event])) {
            return $this->listeners[$event];
        } else {
            return array();
        }
    }

    /**
     * Get a list of events for which this collection has listeners.
     *
     * @return  array
     */
    public function getEvents()
    {
        return array_keys($this->listeners);
    }

    /**
     * @throws  Enlight_Event_Exception
     * @param   string $event
     * @param   Enlight_Event_EventArgs|array|null $eventArgs
     * @return  Enlight_Event_EventArgs|null
     */
    public function notify($event, $eventArgs = null)
    {
        if(!$this->hasListeners($event)) {
            return null;
        }
        if(isset($eventArgs) && is_array($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
        } elseif(!isset($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event);
        } elseif(!$eventArgs instanceof Enlight_Event_EventArgs) {
            throw new Enlight_Event_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
        }
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);
        foreach ($this->getListeners($event) as $listener) {
            $listener->execute($eventArgs);
        }
        $eventArgs->setProcessed(true);
        return $eventArgs;
    }

    /**
     * @throws  Enlight_Exception
     * @param   string $event
     * @param   Enlight_Event_EventArgs|array|null $eventArgs
     * @return  Enlight_Event_EventArgs|null
     */
    public function notifyUntil($event, $eventArgs = null)
    {
        if(!$this->hasListeners($event)) {
            return null;
        }
        if(isset($eventArgs) && is_array($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
        } elseif(!isset($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event);
        } elseif(!$eventArgs instanceof Enlight_Event_EventArgs) {
            throw new Enlight_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
        }
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);
        foreach ($this->getListeners($eventArgs->getName()) as $listener) {
            if (null !== ($return = $listener->execute($eventArgs))
              || $eventArgs->isProcessed()) {
                $eventArgs->setProcessed(true);
                $eventArgs->setReturn($return);
            }
            if($eventArgs->isProcessed()) {
                return $eventArgs;
            }
        }
        return null;
    }

    /**
     * @throws  Enlight_Event_Exception
     * @param   string $event
     * @param   mixed $value
     * @param   Enlight_Event_EventArgs|array|null $eventArgs
     * @return  mixed
     */
    public function filter($event, $value, $eventArgs = null)
    {
        if(!$this->hasListeners($event)) {
            return $value;
        }
        if(isset($eventArgs) && is_array($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event, $eventArgs);
        } elseif(!isset($eventArgs)) {
            $eventArgs = new Enlight_Event_EventArgs($event);
        } elseif(!$eventArgs instanceof Enlight_Event_EventArgs) {
            throw new Enlight_Event_Exception('Parameter "eventArgs" must be an instance of "Enlight_Event_EventArgs"');
        }
        $eventArgs->setReturn($value);
        $eventArgs->setName($event);
        $eventArgs->setProcessed(false);
        foreach ($this->getListeners($event) as $listener) {
            $eventArgs->setReturn($listener->execute($eventArgs));
        }
        $eventArgs->setProcessed(true);
        return $eventArgs->getReturn();
    }

    /**
     * @param   Enlight_Event_Subscriber_Subscriber $subscriber
     * @return  void
     */
    public function addSubscriber(Enlight_Event_Subscriber_Subscriber $subscriber)
    {
        $listeners = $subscriber->getListeners();
        foreach ($listeners as $listener) {
            $this->registerListener($listener);
        }
    }

    /**
     * Resets the event listeners.
     *
     * @return  Enlight_Event_EventManager
     */
    public function resetEvents()
    {
        $this->listeners = array();
        return $this;
    }
}