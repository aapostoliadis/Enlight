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
class Enlight_Event_EventHandler
{
    protected $name;
    protected $listener;
    protected $position;

    /**
     * @throws  Enlight_Exception
     * @param   string $event
     * @param   callback $listener
     * @param   null $position
     */
	public function __construct ($event, $listener, $position=null)
	{
		if(empty($event) || empty($listener)) {
			throw new Enlight_Event_Exception('Some parameters are empty');
		}
		if(!is_callable($listener, true, $listener_event)) {
			throw new Enlight_Event_Exception('Listener "'.$listener_event.'" is not callable');
		}
		$this->name = $event;
		$this->listener = $listener;
        if($position !== null) {
            $this->setPosition($position);
        }
	}

    /**
     * @throws  Enlight_Exception
     * @param   int $position
     * @return  Enlight_Event_EventHandler
     */
	public function setPosition($position)
	{
		if(!is_numeric($position)) {
			throw new Enlight_Exception('Position is not numeric');
		}
		$this->position = (int) $position;
		return $this;
	}

    /**
     * @return  string
     */
	public function getName()
	{
		return $this->name;
	}

    /**
     * @return  callback
     */
	public function getListener()
	{
		return $this->listener;
	}

    /**
     * @return  int
     */
	public function getPosition()
	{
		return $this->position;
	}

    /**
     * @param   array $args
     * @return  mixed
     */
	public function execute($args=null)
	{
		return call_user_func_array($this->listener, (array) $args);
	}

    /**
     * @return  array
     */
	public function toArray()
	{
        $listener = $this->listener;
        if(is_array($listener)) {
            if($listener[0] instanceof Enlight_Singleton) {
                $listener[0] = get_class($listener[0]);
            }
            $listener = implode('::', $listener);
        }
        return array(
            'name' => $this->name,
            'listener' => $listener,
            'position' => $this->position
        );
	}
}