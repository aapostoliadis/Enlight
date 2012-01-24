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
 * The Enlight_Plugin_Bootstrap is the basic class for each plugin bootstrap.
 * It has an reference to the application and the plugin collection.
 *
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Plugin_Bootstrap extends Enlight_Class
{
    /**
     * @var string Contains the name of the plugin.
     */
    protected $name;

    /**
     * @var Enlight_Plugin_PluginCollection Contains an instance of the Enlight_Plugin_PluginCollection
     */
    protected $collection;

    /**
     * The Enlight_Plugin_Bootstrap expects a name for the plugin and
     * optionally an instance of the Enlight_Plugin_PluginCollection
     *
     * @param   Enlight_Plugin_PluginCollection $collection
     * @param                                   $name
     */
    public function __construct($name, $collection = null)
    {
        $this->name = (string) $name;
        $this->setCollection($collection);
        parent::__construct();
    }

    /**
     * Getter method for the plugin name property.
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter method for the collection property.
     * @param   $collection
     * @return  Enlight_Plugin_PluginCollection
     */
    public function setCollection(Enlight_Plugin_PluginCollection $collection=null)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Getter method for the collection property.
     * @return  Enlight_Plugin_PluginCollection
     */
    public function Collection()
    {
        return $this->collection;
    }

    /**
     * Returns the application instance of the collection property.
     *
     * @return  Enlight_Application
     */
    public function Application()
    {
        return $this->collection->Application();
    }
}