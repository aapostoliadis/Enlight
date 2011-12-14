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
 * @package    Enlight_Site
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Site
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Site_Manager
{
    /**
     * @var
     */
    protected $adapter;

    /**
     * @param   array|Enlight_Config|null $options
     */
    public function __construct($options = null)
    {
        if(!is_array($options)) {
            $options = array('adapter' => $options);
        }

        if(isset($options['adapter'])
          && $options['adapter'] instanceof Enlight_Config) {
            $this->adapter = $options['adapter'];
        }
    }

    /**
     * @param   $property
     * @param   $value
     * @return  Enlight_Components_Site|null
     */
    public function findOneBy($property, $value)
    {
        foreach($this->adapter->sites as $site) {
            if($site->$property === $value) {
                return new Enlight_Components_Site($site);
            }
        }
        return null;
    }

    /**
     * @return  Enlight_Components_Site|null
     */
    public function getDefault()
    {
        reset($this->adapter->sites);
        $default = current($this->adapter->sites);
        foreach($this->adapter->sites as $site) {
            if(!empty($site->default)) {
                $default = $site;
                break;
            }
        }
        $default = new Enlight_Components_Site($default);
        return $default;
    }
}