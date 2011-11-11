<?php
class Webenq_Test_Form_Question_EditTest extends Webenq_Test_Form_Question_AddTest
{
    public function testOneLanguageIsRequired()
    {
        $question = new Webenq_Model_Question();

        $this->_form = new Webenq_Form_Question_Edit($question);

        $form = $this->_form;

        // invalid without languages
        $values = array('text' => array(
            'en' => '',
            'nl' => ''));
        $this->assertFalse($form->isValid($values));

        // valid with one language
        $values = array('text' => array(
            'en' => 'test',
            'nl' => ''));
        $this->assertTrue($form->isValid($values));

        // valid with all languages
        $values = array('text' => array(
            'en' => 'test',
            'nl' => 'test'));
        $this->assertTrue($form->isValid($values));
    }
}