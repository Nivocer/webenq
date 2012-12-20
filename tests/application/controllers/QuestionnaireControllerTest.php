<?php
class Webenq_Test_ControllerTestCase_QuestionnaireControllerTest extends Webenq_Test_Case_Controller
{
//    public function testLogin(){
//        $this->getRequest()->setMethod('POST')->setPost(
//            array(
//                'username' => 'admin',
//                'password' => 'webenq'
//            )
//        );
//        $this->dispatch('user/login');
//    }

    public function testQuestionnaireViewIsRendered()
    {
        $this->loadDatabase();

        $this->dispatch('/questionnaire');
        $this->assertController('questionnaire');
        $this->assertQuery('tbody.questionnaire');
    }
//    public function tearDown(){
//        $this->dispatch('user/logout');
//    }
}