<?php
class Webenq_Form_Questionnaire_AddTest extends Webenq_Test_Form
{
    public function testTitleIsRequired()
    {
        $this->assertFalse($this->_form->isValid(array()));
        $this->assertTrue($this->hasErrors($this->_form->title));
    }
}