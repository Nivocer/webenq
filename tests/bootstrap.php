<?php

// define application environment
define('APPLICATION_ENV', 'testing');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');

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