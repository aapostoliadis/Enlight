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
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     Marcel Schmaeing
 * @author     $Author$
 */

/**
 * Application starter
 * This file starts a new Enlight application with a custom configuration
 *
 * @category   Enlight
 * @package    Blog
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
// set the include path to the enlight library and to the application directory
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../../Library/');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../../Apps/');

//include the blog application
include_once 'Blog/Application.php';

/*
 * these are the configuration values for the application
 */
$config = array(
    'app' => 'Blog', //Application name
    'app_path' => '.', //path to the application dir
    'db' => array( //database connection data
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'enlight'
    ),
    'front' => array( // view settings
        'noErrorHandler' => false,
        'throwExceptions' => true,
        'useDefaultControllerAlways' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ));

//creates a new Enlight application instance
$app = new Blog_Application('production', $config);
return $app->run();
