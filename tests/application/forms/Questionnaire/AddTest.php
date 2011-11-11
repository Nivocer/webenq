<?php
class Webenq_Form_Questionnaire_AddTest extends Webenq_Test_Case_Form
{
    public function testFormValidationFailsWhenNoTitleIsSet()
    {
        $this->getForm();
        $this->assertFalse($this->_form->isValid(array()));
        $this->assertTrue($this->hasErrors($this->_form));
    }

    public function testFormValidatesWhenOneTitleIsSet()
    {
        $this->getForm();
        $this->assertTrue($this->_form->isValid(array(
        	'title' => array(
        		'en' => 'test'))));
        $this->assertFalse($this->hasErrors($this->_form));
    }

    public function testFormValidatesWhenTwoTitlesAreSet()
    {
        $this->getForm();
        $this->assertTrue($this->_form->isValid(array(
        	'title' => array(
        		'en' => 'test',
        		'nl' => 'test'))));
        $this->assertFalse($this->hasErrors($this->_form));
    }
}