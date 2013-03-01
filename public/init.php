<?php
// define path to application directory (if not set yet)
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// set include paths
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/classes',
    dirname(APPLICATION_PATH) . '/libraries',
    get_include_path(),
)));

// set-up application with the right environment and configuration
require_once 'Webenq/Application.php';
Webenq_Application::$defaultConfig = APPLICATION_PATH . '/configs/application.ini';
Webenq_Application::$overrideConfig = APPLICATION_PATH . '/configs/override.ini';
$application = new Webenq_Application(APPLICATION_ENV);