<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap('doctrine');
$config = $application->getOption('doctrine');

// rebuild database and load data
$cli = new Doctrine_Cli($config);
$cli->run(array('', 'rebuild-db'));
$cli->run(array('', 'load-data', $config['models_path'], $config['data_fixtures_path'], 'append'));

// set current schema version
$config = $application->getBootstrap()->getOption('doctrine');
$migration = new Doctrine_Migration($config['migrations_path']);
$version = $migration->getLatestVersion();
$version = new Webenq_Model_MigrationVersion();
$version->version = (int) $migration->getLatestVersion();
$version->save();
