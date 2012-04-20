#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');
$application->bootstrap('doctrine');
$configuration = $application->getOption('doctrine');

// run
$cli = new Doctrine_Cli($configuration);
$cli->run($_SERVER['argv']);

// fix dumped YAML
if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'dump-data') {
    require_once realpath(dirname(__FILE__) . '/fixYaml.php');
    $fixYaml = new FixYaml($configuration['data_fixtures_path']);
    $fixYaml->run();
    echo "remember to remove session data from fixture (application/doctrine/fixture/data.yml)";
}
