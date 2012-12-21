<?php
class Webenq_Test_ControllerTestCase_QuestionnaireControllerTest extends Webenq_Test_Case_Controller
{
    public function testCorrectControllerIsUsed()
    {
        $this->dispatch('/questionnaire');
        $this->assertController('questionnaire');
    }

    public function testQuestionnaireViewIsRendered()
    {
        $this->loadDatabase();
        $this->dispatch('/questionnaire');
        $this->assertQuery('tbody.questionnaire');

    }
}