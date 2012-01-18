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
 * @param $str
 * @param int $width
 * @param string $break
 * @param string $fill
 * @return string
 */
function smarty_modifier_padding ($str, $width=10, $break='...', $fill=' ')
{
	if(!is_scalar($break)) {
		$break = '...';
	}
	if(empty($fill) || !is_scalar($fill)) {
		$fill = ' ';
	}
	if(empty($width) || !is_numeric($width)) {
		$width = 10;
	} else { 
		$width = (int) $width;
	}
    
    //printf('% ' . $width . ' s' . $str);

	if(!is_scalar($str)) {
		return str_repeat($fill, $width);
	}
	if(strlen($str) > $width) {
		$str = substr($str, 0, $width - strlen($break)) . $break;
	}
	if($width > strlen($str)) {
		return str_repeat($fill,$width-strlen($str)) . $str;
	} else { 
		return $str;
	}
}