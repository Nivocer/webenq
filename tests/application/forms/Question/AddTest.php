<?php
class Webenq_Test_Form_Question_AddTest extends Webenq_Test_Case_Form
{
    public function testOneLanguageIsRequired()
    {
        $form = $this->getForm();

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