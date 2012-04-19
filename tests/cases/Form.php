<?php
class Webenq_Test_Case_Form extends PHPUnit_Framework_TestCase
{
    protected $_form;

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
     * @param Zend_Form|Zend_Form_Element $form
     * @return boolean
     */
    public function hasErrors($form)
    {
        if ($form instanceof Zend_Form_Element) {
            return $this->_elementHasErrors($form);
        } elseif ($form instanceof Zend_Form) {
            return $this->_formHasErrors($form);
        }
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