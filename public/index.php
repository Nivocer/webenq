<?php

// define application environment (if not set yet)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// initialize
require_once realpath(dirname(__FILE__) . '/init.php');

// create application, bootstrap, and run
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, $config->{APPLICATION_ENV});
$application->bootstrap()->run();