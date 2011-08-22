#!/usr/bin/env php
<?php

// define application environment
define('APPLICATION_ENV', 'development');

// initialize
require_once realpath(dirname(__FILE__) . '/../public/init.php');

$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('doctrine');

$config = $bootstrap->getOption('doctrine');

$migration = new Doctrine_Migration($config['migrations_path']);
$currentVersion = $migration->getCurrentVersion();

$files = scandir($config['schema_path']);
if ($key = array_search($currentVersion . '.yml', $files)) {
    $current = $config['schema_path'] . DIRECTORY_SEPARATOR . $files[$key];
} else {
    throw new Exception('Schema for current version not found!');
}

$nextVersion = 1 + $currentVersion;
if ($key = array_search($nextVersion . '.yml', $files)) {
    $next = $config['schema_path'] . DIRECTORY_SEPARATOR . $files[$key];
} else {
    throw new Exception('Schema for next version not found!');
}

Doctrine_Core::generateMigrationsFromDiff($config['migrations_path'], $current, $next);

// migrate if new version was created
if ((int) $migration->getLatestVersion() >= (int) $nextVersion) {
    $migration->migrate($nextVersion);
}