<?php

/* Set time zone */
if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Europe/Amsterdam');
}

/* Define path to application directory */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

/* Define application environment */
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/* Ensure library/ is on include_path */
set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../libraries'),
	realpath(APPLICATION_PATH . '/../classes'),
    get_include_path(),
)));

/* Zend_Application */
require_once 'Zend/Application.php';

/* Create application, bootstrap, and run */
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()->run();
