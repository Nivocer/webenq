<?php
class Webenq_Test_Case_Model extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $application;
        $application->bootstrap();

        parent::setUp();

        $config = $application->getBootstrap()->getOption('doctrine');
        Doctrine_Core::createTablesFromModels($config['models_path']);
        Doctrine_Core::loadData($config['data_fixtures_path'] . '/testing.yml');
    }
}