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
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../../Library/');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../../Apps/');

//include the blog application
include_once 'Blog/Application.php';

/*
 * these are the configuration values for the application
 */
$config = array(

    // Application name
    'app' => 'Blog',

    // path to the application dir
    'app_path' => '.',

    // database adapter
    'adapter' => 'PDO_MYSQL',

    // database connection data
    'db' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'dbname' => 'enlightBlog',
        'charset' => 'utf8'
    ),

    // php settings
    'phpSettings' => array(
        'error_reporting' => E_ALL | E_STRICT,
        'display_errors' => 1,
        'date.timezone' => 'Europe/Berlin',
        'zend.ze1_compatibility_mode' => 0
    ),

    // view settings
    'front' => array(
        'noErrorHandler' => false,
        'throwExceptions' => true,
        'useDefaultControllerAlways' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    )
);

// creates a new Enlight application instance
$app = new Blog_Application('production', $config);
return $app->run();
