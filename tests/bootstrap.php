<?php

error_reporting( E_ALL | E_STRICT );
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Amsterdam');

// Define test path
define('TESTS_PATH', realpath(dirname(__FILE__)));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define path to library directory
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', '/usr/share/php/libzend-framework-php');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIBRARY_PATH),
    realpath(APPLICATION_PATH . '/../../nivocer-thirdparty/odtphp'),
    get_include_path(),
)));


/* set up auto-loading */
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('HVA_');
$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	'basePath'  => APPLICATION_PATH,
	'namespace' => 'HVA',
));
$resourceLoader->addResourceType('model', 'models', 'Model');
$resourceLoader->addResourceType('form', 'forms', 'Form');
$resourceLoader->addResourceType('test', '../tests/application', 'Test');
$resourceLoader->addResourceType('test_model', '../tests/application/models', 'Test_Model');


Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();


$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();