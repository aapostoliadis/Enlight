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
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @param null $value
 * @param null $path
 * @param null $locale
 * @return null
 */
function smarty_modifier_translate ($value = null, $path = null, $locale = null)
{
	if(!Enlight_Application::Instance()->Bootstrap()->hasResource('Locale')) {
		return $value;
	}
	if($locale === null) {
		$locale = Enlight_Application::Instance()->Locale();
	}
	if($path=='currency') {
		$path = 'nametocurrency';
	}
    return $locale->getTranslation($value, $path, $locale);
}