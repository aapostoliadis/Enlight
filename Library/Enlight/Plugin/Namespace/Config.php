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
class Enlight_Plugin_Namespace_Config extends Enlight_Plugin_Namespace
{
    /**
     * @param   $name
     * @return  Enlight_Plugin_Bootstrap
     */
    public function get($name)
    {
        if(!$this->plugins->offsetExists($name)) {
            $this->load($name);
        }
        return $this->plugins->offsetGet($name);
    }

    /**
     * @param   $name
     * @return  bool
     */
    public function load($name)
    {

    }

    /**
     * @return  Enlight_Plugin_PluginNamespace
     */
    public function loadAll()
    {
        
        return $this;
    }
}