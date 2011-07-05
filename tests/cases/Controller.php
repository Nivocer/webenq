<?php
abstract class Webenq_Test_Controller extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $application->bootstrap();

        /**
         * Fix for ZF-8193
         * http://framework.zend.com/issues/browse/ZF-8193
         * Zend_Controller_Action->getInvokeArg('bootstrap') doesn't work
         * under the unit testing environment.
         */
        $front = Zend_Controller_Front::getInstance();
        if ($front->getParam('bootstrap') === null) {
            $front->setParam('bootstrap', $application->getBootstrap());
        }
    }

    public function tearDown()
    {
        $this->reset();
    }
}