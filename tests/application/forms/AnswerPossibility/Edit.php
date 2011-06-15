<?php
class Webenq_Test_Form_AnswerPossibility_Edit extends Webenq_Test_Form_AnswerPossibility
{
    protected $_form;

    public function setUp()
    {
        parent::setUp();

        $answerPossibility = new AnswerPossibility();
        $answerPossibility->id = 1;
        $answerPossibility->active = true;
        $answerPossibility->answerPossibilityGroup_id = 1;
        $answerPossibility->value = 'test';
        $this->_form = new Webenq_Form_AnswerPossibility_Edit($answerPossibility);
    }

    public function testTextIsRequired()
    {
        $values = array('text' => '');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->text));
    }

    public function testValueIsRequiredAndMustBeInteger()
    {
        $values = array('value' => '');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->value));

        $values = array('value' => 'test');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->value));

        $values = array('value' => '1');
        $this->_form->isValid($values);
        $this->assertFalse($this->hasErrors($this->_form->value));
    }
}