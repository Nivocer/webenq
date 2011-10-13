<?php
class Webenq_Form_Questionnaire_AddTest extends Webenq_Test_Case_Form
{
    public function testTitleIsRequired()
    {
        $this->getForm();
        $this->assertFalse($this->_form->isValid(array()));
        $this->assertTrue($this->hasErrors($this->_form->title));
    }
}