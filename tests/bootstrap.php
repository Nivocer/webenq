<?php

// define application environment
define('APPLICATION_ENV', 'testing');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');

// set up auto-loading
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Webenq_');
$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	'basePath'  => APPLICATION_PATH,
	'namespace' => 'Webenq',
));
//$resourceLoader->addResourceType('model', 'models', 'Model');
//$resourceLoader->addResourceType('form', 'forms', 'Form');
$resourceLoader->addResourceType('tests', '../tests/application', 'Test');
$resourceLoader->addResourceType('controllerTestCases', '../tests/application/controllers', 'Test_Controller');
$resourceLoader->addResourceType('modelTestCases', '../tests/application/models', 'Test_Model');
$resourceLoader->addResourceType('formTestCases', '../tests/application/forms', 'Test_Form');
$resourceLoader->addResourceType('pluginTestCases', '../tests/application/plugins', 'Test_Plugin');

// create and bootstrap application
$application = new Zend_Application(APPLICATION_ENV, $config->{APPLICATION_ENV});
$application->bootstrap();

require_once 'cases/Controller.php';
require_once 'cases/Model.php';
require_once 'cases/Form.php';
require_once 'cases/Plugin.php';