<?php

// define application environment (if not set yet)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// initialize
require_once realpath(dirname(__FILE__) . '/init.php');

// run application
$application->run();