<?php

// define application environment
define('APPLICATION_ENV', 'testing');

// define path to application directory (if not set yet)
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

//make sure override.ini exist
if (!file_exists(APPLICATION_PATH . '/configs/override.ini')) {
    copy(APPLICATION_PATH . '/configs/override.ini.sample',
    APPLICATION_PATH . '/configs/override.ini');
}

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap();

// set up auto-loading for tests
$loader = Zend_Loader_Autoloader::getInstance();
$namespaceLoaders = $loader->getNamespaceAutoloaders('Webenq_');
$webenqLoader = $namespaceLoaders[0];
$webenqLoader->addResourceType('testCases', '../tests/cases', 'Test_Case');
$webenqLoader->addResourceType('testClasses', '../tests/application/classes', 'Test_Class');
$webenqLoader->addResourceType('testControllers', '../tests/application/controllers', 'Test_Controller');
$webenqLoader->addResourceType('testForms', '../tests/application/forms', 'Test_Form');
$webenqLoader->addResourceType('testModels', '../tests/application/models', 'Test_Model');
$webenqLoader->addResourceType('testPlugins', '../tests/application/plugins', 'Test_Plugin');
