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
 * @category    Enlight
 * @package	    Enlight_Components_Menu
 * @copyright   Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license	    http://enlight.de/license	 New BSD License
 * @version	    $Id$
 * @author	    Heiner Lohaus
 * @author	    $Author$
 */

/**
 * @category    Enlight
 * @package	    Enlight_Components_Menu
 * @copyright   Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license	    http://enlight.de/license	 New BSD License
 */
class Enlight_Components_Menu_Item extends Zend_Navigation_Page_Uri
{
    /**
     * @static
     * @param   Enlight_Config|array $options
     * @return  Enlight_Components_Menu_Item
     */
	public static function factory($options)
    {
    	if ($options instanceof Zend_Config) {
            /** @var $options Zend_Config */
            $options = $options->toArray();
        }
        $options['type'] = __CLASS__;
        return parent::factory($options);
    }

    /**
     * @param   $page
     * @return  Enlight_Components_Menu_Item
     */
    public function addItem($page)
	{
		return $this->addPage($page);
	}

    /**
     * @param   $pages
     * @return  Enlight_Components_Menu_Item
     */
	public function addItems($pages)
	{
		return $this->addPages($pages);
	}
}