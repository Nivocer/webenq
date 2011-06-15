<?php
class Webenq_Test_Form_AnswerPossibilityTest extends Webenq_Test_Form
{
    protected $_form;

    public function setUp()
    {
        parent::setUp();

        $answerPossibilityGroup = new AnswerPossibilityGroup();
        $answerPossibilityGroup->id = 1;
        $language = 'nl';
        $this->_form = new Webenq_Form_AnswerPossibility_Add($answerPossibilityGroup, $language);
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