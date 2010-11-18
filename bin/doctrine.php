#!/usr/bin/env php
<?php
define('APPLICATION_ENV', 'development');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../libraries'),
    get_include_path(),
)));

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$bootstrap = $application->getBootstrap();
$config = $bootstrap->getOption('resources');
$db = $config['db']['params'];
$url = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] . '/' .  $db['dbname'];
$conn = Doctrine_Manager::connection($url, 'doctrine');

$cli = new Doctrine_Cli($bootstrap->getOption('doctrine'));
$cli->run($_SERVER['argv']);
