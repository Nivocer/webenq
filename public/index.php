<?php

// define application environment (if not set yet)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// initialize
require_once realpath(dirname(__FILE__) . '/init.php');

// bootstrap and run application
$application->bootstrap()->run();

//fix spl_autoload problem, if the server use some optimalization
register_shutdown_function(array('Zend_Session', 'writeClose'), true);
