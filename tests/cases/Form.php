<?php
class Webenq_Test_Form extends PHPUnit_Framework_TestCase
{
    public $application;

    public function setUp()
    {
        $this->application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $this->bootstrap = array($this->application->getBootstrap(), 'bootstrap');
        parent::setUp();
    }

    public function tearDown() {}

    public function hasErrors(Zend_Form_Element $element)
    {
        return (count($element->getErrors()) > 0);
    }
}