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
 * @package    Enlight_Template_Plugins
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */
/**
 * Returns the current encoding (see: mb_internal_encoding())
 * 
 * @link http://php.net/manual/en/function.mb-internal-encoding.php
 * 
 * @param $params
 * @param $smarty
 * @param $template
 * @return mixed
 */
function smarty_function_encoding($params, $smarty, $template)
{
	return mb_internal_encoding();
}