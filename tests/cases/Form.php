<?php
class Webenq_Test_Case_Form extends PHPUnit_Framework_TestCase
{
    protected $_form;

    public function setUp()
    {
        global $application;
        $application->bootstrap();

        parent::setUp();

        $config = $application->getBootstrap()->getOption('doctrine');
        Doctrine_Core::createTablesFromModels($config['models_path']);
        Doctrine_Core::loadData($config['fixtures_path'] . '/testing.yml');
    }

    public function getForm()
    {
        if (!is_object($this->_form)) {
            $formClass = get_class($this);
            $formClass = str_replace('Test_', null, $formClass);
            $formClass = str_replace('Test', null, $formClass);
            $this->_form = new $formClass;
        }
        return $this->_form;
    }

    public function hasErrors(Zend_Form_Element $element)
    {
        return (count($element->getErrors()) > 0);
    }
}