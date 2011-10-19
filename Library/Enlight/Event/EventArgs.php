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
class Enlight_Event_EventArgs extends Enlight_Collection_ArrayCollection
{
    /**
     * @var bool
     */
    protected $_processed;

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var mixed
     */
	protected $_return;

    /**
     * @param   $name
     * @param   array|null $args
     */
    public function __construct($name, array $args=null)
	{
		$this->_name = $name;
        parent::__construct($args);
	}

    /**
     * @return  Enlight_Event_EventArgs
     */
    public function stop()
	{
		$this->_processed = true;
        return $this;
	}

    /**
     * @param   $processed
     * @return  Enlight_Event_EventArgs
     */
	public function setProcessed($processed)
	{
		$this->_processed = (bool) $processed;
        return $this;
	}

    /**
     * @return bool
     */
	public function isProcessed()
	{
		return $this->_processed;
	}

    /**
     * @param   $name
     * @return  string
     */
	public function setName($name)
	{
		$this->_name = $name;
	}

    /**
     * @return string
     */
    public function getName()
	{
		return $this->_name;
	}

    /**
     * @param   mixed $return
     * @return  void
     */
	public function setReturn($return)
	{
		$this->_return = $return;
	}

    /**
     * @return  mixed
     */
	public function getReturn()
	{
		return $this->_return;
	}
}