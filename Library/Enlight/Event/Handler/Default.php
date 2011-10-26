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
class Enlight_Event_Handler_Default extends Enlight_Event_Handler
{
    /**
     * @var callback
     */
    protected $listener;

    /**
     * @throws  Enlight_Exception
     * @param   string $event
     * @param   callback $listener
     * @param   null $position
     */
	public function __construct($event, $position=null, $listener)
	{
		parent::__construct($event, $position);
        if(!is_callable($listener, true, $listener_event)) {
			throw new Enlight_Event_Exception('Listener "'.$listener_event.'" is not callable');
		}
        $this->listener = $listener;
	}

    /**
     * @return  callback
     */
	public function getListener()
	{
		return $this->listener;
	}

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return  mixed
     */
	public function execute(Enlight_Event_EventArgs $args)
	{
		return call_user_func($this->listener, $args);
	}
}