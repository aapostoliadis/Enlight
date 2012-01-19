<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/Library/');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/Apps/');
include_once 'Blog/Application.php';

$config = array(
    'app' => 'Blog',
    'db' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'enlight'
    ),
    'front' => array(
        'noErrorHandler' => false,
        'throwExceptions' => true,
        'useDefaultControllerAlways' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ));

$app = new Blog_Application('production', $config);
return $app->run();
