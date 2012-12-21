<?php
class Webenq_Test_Case_Fixture extends PHPUnit_Framework_TestCase
{
    public $setupDatabase = false;

    public function createDatabase() {
        $this->application = new Webenq_Application(APPLICATION_ENV);
        $this->doctrineConfig = $this->application->getBootstrap()->getOption('doctrine');

        // set up database for testing
        Doctrine_Core::createDatabases();
        Doctrine_Core::createTablesFromModels($this->doctrineConfig['models_path']);
    }

    public function loadDatabase() {
        if (!$this->setupDatabase) {
            $this->createDatabase();
        }
        Doctrine_Core::loadData($this->doctrineConfig['data_fixtures_path'], false);
    }

    public function setUp()
    {
        parent::setUp();

        if ($this->setupDatabase) {
            $this->createDatabase();
        }
    }

    public function tearDown()
    {
        try {
            Doctrine_Core::dropDatabases();
        } catch (Exception $e) {
        }

        parent::tearDown();
    }
}