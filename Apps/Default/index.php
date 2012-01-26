<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/../../Library/'));

include_once 'Enlight/Application.php';

$app = new Enlight_Application('production', array(
    'app' => 'Default',
    'appPath' => '.',
    'phpSettings' => array(
        'error_reporting' => E_ALL | E_STRICT,
        'display_errors' => 1,
        'date.timezone' => 'Europe/Berlin',
        'zend.ze1_compatibility_mode' => 0
    ),
    'front' => array(
        'noErrorHandler' => false,
        'throwExceptions' => true,
        'useDefaultControllerAlways' => true,
        'disableOutputBuffering' => false,
        'showException' => true,
    ),
));

return $app->run();