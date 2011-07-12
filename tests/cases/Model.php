<?php
class Webenq_Test_Model extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $application;

        $config = $application->getBootstrap()->getOption('doctrine');
        Doctrine_Core::createTablesFromModels($config['models_path']);
        Doctrine_Core::loadData($config['data_fixtures_path'] . '/testing.yml');

        parent::setUp();
    }
}