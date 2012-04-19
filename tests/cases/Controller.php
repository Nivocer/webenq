<?php
abstract class Webenq_Test_Case_Controller extends Zend_Test_PHPUnit_ControllerTestCase
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

        $this->getFrontController()->setControllerDirectory(APPLICATION_PATH . '/controllers');
    }

    public function tearDown()
    {
        global $testingDatabase;
        unlink($testingDatabase);
        $this->reset();
        parent::tearDown();
    }
}