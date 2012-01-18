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
abstract class Enlight_Plugin_Namespace extends Enlight_Plugin_PluginCollection
{
    /**
     * @var Enlight_Plugin_PluginManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param   string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the application instance.
     *
     * @return Enlight_Application
     */
    public function Application()
    {
        return $this->manager->Application();
    }

    /**
     * @param Enlight_Plugin_PluginManager $manager
     * @return Enlight_Plugin_PluginManager
     */
    public function setManager(Enlight_Plugin_PluginManager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Returns the application instance.
     *
     * @return  Enlight_Plugin_PluginManager
     */
    public function Manager()
    {
        return $this->manager;
    }
}