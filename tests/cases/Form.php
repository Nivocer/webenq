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
        Doctrine_Core::dropDatabases();
        Doctrine_Core::createDatabases();
        Doctrine_Core::createTablesFromModels($config['models_path']);
        Doctrine_Core::loadData($config['data_fixtures_path']);
    }

    public function getForm()
    {
        if (!is_object($this->_form)) {
            $formClass = get_class($this);
            $formClass = str_replace('Test_', null, $formClass);
            $formClass = str_replace('Test', null, $formClass);
            $this->_form = new $formClass;
        }

        $this->_form->reset();
        return $this->_form;
    }

    /**
     * Scans all subforms and elements for errors
     *
     * @param Zend_Form $element
     * @return boolean
     */
    public function hasErrors(Zend_Form $form)
    {
        if ($form instanceof Zend_Form_Element) {
            return $this->_elementHasErrors($form);
        }

        return $this->_formHasErrors($form);
    }

    protected function _formHasErrors(Zend_Form $form)
    {
        foreach ($form->getSubForms() as $subForms) {
            if ($this->_formHasErrors($subForms)) return true;
        }

        foreach ($form->getElements() as $element) {
            if ($this->_elementHasErrors($element)) return true;
        }

        return false;
    }

    protected function _elementHasErrors(Zend_Form_Element $element)
    {
        return (count($element->getErrors()) > 0);
    }
}