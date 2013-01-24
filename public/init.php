<?php

// set default time zone
// @todo default timezone setting should move into config
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/Amsterdam');
}

// define path to application directory (if not set yet)
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARIES_PATH')
|| define('LIBRARIES_PATH', realpath(dirname(__FILE__) . '/../libraries'));

// set include paths
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/classes'),
    LIBRARIES_PATH,
    get_include_path(),
    )
));

// set-up application with the right environment and configuration
require_once APPLICATION_PATH . '/classes/Webenq/Application.php';
Webenq_Application::$defaultConfig = APPLICATION_PATH . '/configs/application.ini';
Webenq_Application::$overrideConfig = APPLICATION_PATH . '/configs/override.ini';
$application = new Webenq_Application(APPLICATION_ENV);