<?php

// set default time zone
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Europe/Amsterdam');
}

// define path to application directory (if not set yet)
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// set include paths
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../libraries'),
	realpath(APPLICATION_PATH . '/../classes'),
    get_include_path(),
)));

// get configuration
require_once 'Zend/Config/Ini.php';
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', null, array('allowModifications' => true));
if (file_exists(APPLICATION_PATH . '/configs/override.ini')) {
    $override = new Zend_Config_Ini(APPLICATION_PATH . '/configs/override.ini');
    $config->merge($override)->setReadOnly();
}
if (!$config->{APPLICATION_ENV}) {
    throw new Exception('No configuration found for application environment "' . APPLICATION_ENV . '"');
}

// bootstrap application
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, $config->{APPLICATION_ENV});
$application->bootstrap();