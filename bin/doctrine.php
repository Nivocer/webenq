#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');

// create and bootstrap application
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, $config->{APPLICATION_ENV});
$application->bootstrap();

$bootstrap = $application->getBootstrap();
$config = $bootstrap->getOption('resources');
$db = $config['db']['params'];
$url = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] . '/' .  $db['dbname'];
$conn = Doctrine_Manager::connection($url, 'doctrine');

$cli = new Doctrine_Cli($bootstrap->getOption('doctrine'));
$cli->run($_SERVER['argv']);