<?php
class Webenq_Test_Model_QuestionnaireQuestionTest extends Webenq_Test_Case_Model
{
    public function testFormElementIsGenerated()
    {
        $qq = new Webenq_Model_QuestionnaireQuestion();
        $elm = $qq->getFormElement();
        $this->assertTrue($elm instanceof Zend_Form_Element);
    }
}