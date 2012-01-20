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
 * Build an link based on given controller and action name
 *
 * Parameters known by $params
 * - module     : name of the module
 * - controller : name of the controller
 * - action     : name of the action
 *
 * @param $params
 * @param $smarty
 * @return mixed
 */
function smarty_function_url($params, $smarty)
{
    $front = Enlight_Application::Instance()->Bootstrap()->getResource('Front');

    return $front->Router()->assemble($params);
}