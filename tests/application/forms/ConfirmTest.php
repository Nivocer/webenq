<?php
class Webenq_Test_Form_ConfirmTest extends Webenq_Test_Case_Form
{
    protected $_id = 123;

    protected $_text = 'Are you sure?';

    public function setUp()
    {
        $this->_form = new Webenq_Form_Confirm($this->_id, t($this->_text));
        parent::setUp();
    }

    public function testIdAndTextAreSet()
    {
        $this->assertTrue($this->_form->id->getValue() == $this->_id);
        $this->assertTrue($this->_form->id->getLabel() == t($this->_text));
    }
}
