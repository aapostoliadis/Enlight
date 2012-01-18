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
abstract class Enlight_Event_Handler
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @throws  Enlight_Event_Exception
     * @param   $event
     */
    public function __construct($event)
    {
        if ($event === null) {
            throw new Enlight_Event_Exception('Parameter event cannot be empty.');
        }
        $this->name = $event;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return  integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param   integer $position
     * @return  Enlight_Event_Handler
     */
    public function setPosition($position)
    {
        $this->position = (int)$position;
        return $this;
    }

    /**
     * @param   Enlight_Event_EventArgs $args
     * @return  mixed
     */
    abstract public function execute(Enlight_Event_EventArgs $args);
}