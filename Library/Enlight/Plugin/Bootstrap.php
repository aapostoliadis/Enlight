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
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Plugin_Bootstrap extends Enlight_Class
{
    /**
     * @var string
     */
	protected $name;

    /**
     * @var Enlight_Plugin_PluginCollection
     */
	protected $collection;

    /**
     * @param   string $name
     * @param   Enlight_Plugin_PluginCollection $collection
     */
    public function __construct($name, $collection = null)
    {
        $this->name = $name;
        $this->setCollection($collection);
        parent::__construct();
    }

    /**
     * @return  string
     */
	public function getName()
	{
		return $this->name;
	}

    /**
     * @param   $collection
     * @return  Enlight_Plugin_PluginCollection
     */
	public function setCollection($collection)
	{
		$this->collection = $collection;
        return $this;
	}

    /**
     * @return  Enlight_Plugin_PluginCollection
     */
	public function Collection()
	{
		return $this->collection;
	}

    /**
     * Returns the application instance.
     *
     * @return  Enlight_Application
     */
    public function Application()
    {
        return $this->collection->Application();
    }
}