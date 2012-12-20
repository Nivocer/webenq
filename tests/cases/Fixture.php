<?php
class Webenq_Test_Case_Fixture extends PHPUnit_Framework_TestCase
{
    public function loadDatabase() {
        global $doctrineConfig;
        Doctrine_Core::loadData($doctrineConfig['data_fixtures_path'], false);
    }

    public function setUp()
    {
        parent::setUp();

        global $doctrineConfig;
        Doctrine_Core::createDatabases();
        Doctrine_Core::createTablesFromModels($doctrineConfig['models_path']);
    }

    public function tearDown()
    {
        try {
            Doctrine_Core::dropDatabases();
            $this->databaseExists = false;
        } catch (Exception $e) {
        }

        parent::tearDown();
    }
}