<?php
class Webenq_Test_Form_ConfirmTest extends Webenq_Test_Case_Form
{
    protected $_id = 123;

    protected $_text = 'Weet u het zeker?';

    public function setUp()
    {
        $this->_form = new Webenq_Form_Confirm($this->_id, $this->_text);
        parent::setUp();
    }

    public function testIdAndTextAreSet()
    {
        $this->assertTrue($this->_form->id->getValue() == $this->_id);
        $this->assertTrue($this->_form->id->getLabel() == $this->_text);
    }
}