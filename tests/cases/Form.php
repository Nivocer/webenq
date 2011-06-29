<?php
class Webenq_Test_Form extends PHPUnit_Framework_TestCase
{
    public function hasErrors(Zend_Form_Element $element)
    {
        return (count($element->getErrors()) > 0);
    }
}