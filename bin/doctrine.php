#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap('doctrine');

$cli = new Doctrine_Cli($application->getOption('doctrine'));
$cli->run($_SERVER['argv']);
