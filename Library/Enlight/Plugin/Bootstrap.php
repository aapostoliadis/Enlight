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
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Plugin
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
abstract class Enlight_Plugin_PluginBootstrap extends Enlight_Class implements Enlight_Singleton
{
    /**
     * @var Enlight_Plugin_PluginCollection
     */
	protected $namespace;

    /**
     * @var string
     */
	protected $name;

    /**
     * @param   Enlight_Plugin_PluginCollection $namespace
     * @param   $name
     */
	public function __construct(Enlight_Plugin_PluginCollection $namespace, $name)
	{
		$this->namespace = $namespace;
		$this->name = $name;
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
     * @param   $namespace
     * @return  Enlight_Plugin_PluginCollection
     */
	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
        return $this;
	}

    /**
     * @return  Enlight_Plugin_PluginCollection
     */
	public function getNamespace()
	{
		return $this->namespace;
	}
}