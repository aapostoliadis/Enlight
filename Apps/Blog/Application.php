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
 * @package    Blog
 * @subpackage Blog_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     Marcel Schmaeing
 * @author     $Author$
 */

include_once 'Enlight/Application.php';

/**
 * The concrete Blog Application extends Enlight Application
 * Wrapper to allocate the Db instance globally
 *
 * The Db Resource can be accessed in any Application context like this :
 * <code>
 * Blog()->Db()->function
 * </code>
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Application extends Enlight_Application
{
    /**
     * helper to return the Db Resource
     *
     * @return Enlight_Components_Db_Adapter_Pdo_Mysql Db Resource
     */
    public function Db()
    {
        return $this->Bootstrap()->getResource('Db');
    }
}

/**
 * helper to allocate the Blog_Application instance to the global context
 *
 * @return Blog_Application Blog_Application instance
 */
function Blog()
{
    return Blog_Application::Instance();
}
