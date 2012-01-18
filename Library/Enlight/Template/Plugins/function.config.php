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
 * Function to get access to the Enlight2 Config system.
 *
 * The params array knows the key
 * - name    : Name of the config parameter which should be requested
 * - default : Default value if the queried config key does not exists
 *
 * @param array  $params
 * @param mixed  $smarty
 * @param string $template
 *
 * @return mixed
 */
function smarty_function_config($params, $smarty, $template)
{
    $config = Enlight_Application::Instance()->Bootstrap()->hasResource('Config');

    if (empty($params['name']) || $config) {
        return null;
    }
    return Enlight_Application::Instance()->Config()->get(
        $params['name'],
        isset($params['default']) ? $params['default'] : null
    );
}