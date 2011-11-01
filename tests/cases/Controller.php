<?php
abstract class Webenq_Test_Case_Controller extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        global $application;
        $application->bootstrap();

        parent::setUp();

        $config = $application->getBootstrap()->getOption('doctrine');
        Doctrine_Core::createTablesFromModels($config['models_path']);
        Doctrine_Core::loadData($config['data_fixtures_path']);

        $this->getFrontController()->setControllerDirectory(APPLICATION_PATH . '/controllers');
    }

    public function tearDown()
    {
        $this->reset();
    }
}