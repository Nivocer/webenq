<?php
class Webenq_Test_Case_Class extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        // backup initial database (if any) for better performance
        global $doctrineConfig, $initialDatabase, $testingDatabase;
        if (isset($initialDatabase) && file_exists($initialDatabase)) {
            $copied = copy($initialDatabase, $testingDatabase);
        }

        if (!isset($copied) || $copied == false) {
            Doctrine_Core::loadData($doctrineConfig['data_fixtures_path'], false);
        }
    }

    public function tearDown()
    {
        global $testingDatabase;
        unlink($testingDatabase);
        parent::tearDown();
    }
}