<?php

// define application environment
define('APPLICATION_ENV', 'testing');

//make sure override.ini exist
if (!file_exists(realpath(dirname(__FILE__)) . '/../application/configs/override.ini')) {
    copy(realpath(dirname(__FILE__) . '/../application/configs/override.ini.sample'),
	realpath(dirname(__FILE__)) . '/../application/configs/override.ini'  );
}

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap();

// set up database for testing
$doctrineConfig = $application->getBootstrap()->getOption('doctrine');
try {
    Doctrine_Core::dropDatabases();
} catch (Exception $e) {
}
Doctrine_Core::createDatabases();
Doctrine_Core::createTablesFromModels($doctrineConfig['models_path']);
Doctrine_Core::loadData($doctrineConfig['data_fixtures_path'], false);

// make copy of sqlite database file (if any) for better performance
$dbConfig = $application->getBootstrap()->getOption('db');
if (isset($dbConfig['params']['dbname']) && !empty($dbConfig['params']['dbname'])) {
    $testingDatabase = $dbConfig['params']['dbname'];
} elseif (isset($dbConfig['params']['dsn']) && !empty($dbConfig['params']['dsn'])) {
    $info = Doctrine_Manager::getInstance()->parseDsn($dbConfig['params']['dsn']);
    $testingDatabase = $info['path'];
}
if (isset($testingDatabase) && file_exists($testingDatabase)) {
    $initialDatabase = 'init_' . $testingDatabase;
    copy($testingDatabase, $initialDatabase);
}

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
