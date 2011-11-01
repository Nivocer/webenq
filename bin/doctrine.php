#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap();

$bootstrap = $application->getBootstrap();
$config = $bootstrap->getOption('resources');
$db = $config['db']['params'];
$dsn = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] . '/' .  $db['dbname'];
Doctrine_Manager::connection($dsn, 'doctrine');

$cli = new Doctrine_Cli($bootstrap->getOption('doctrine'));
$cli->run($_SERVER['argv']);