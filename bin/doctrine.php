#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap();

$bootstrap = $application->getBootstrap();
$cli = new Doctrine_Cli($bootstrap->getOption('doctrine'));
$cli->run($_SERVER['argv']);