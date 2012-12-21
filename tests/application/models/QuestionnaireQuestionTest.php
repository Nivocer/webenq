<?php
class Webenq_Test_Model_QuestionnaireQuestionTest extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    public function testFormElementIsGenerated()
    {
        $questionnaire = new Webenq_Model_Questionnaire();
        $questionnaire->save();

        $question = new Webenq_Model_Question();
        $question->save();

        $questionnaireQuestion = new Webenq_Model_QuestionnaireQuestion();
        $questionnaireQuestion->Questionnaire = $questionnaire;
        $questionnaireQuestion->Question = $question;
        $questionnaireQuestion->save();

        $element = $questionnaireQuestion->getFormElement();
        $this->assertTrue($element instanceof Zend_Form_Element);
    }
}