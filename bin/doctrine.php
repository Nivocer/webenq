#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');

$bootstrap = $application->getBootstrap();
$config = $bootstrap->getOption('resources');
$db = $config['db']['params'];
$url = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] . '/' .  $db['dbname'];
$conn = Doctrine_Manager::connection($url, 'doctrine');

$cli = new Doctrine_Cli($bootstrap->getOption('doctrine'));
$cli->run($_SERVER['argv']);