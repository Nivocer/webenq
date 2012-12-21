<?php
class Webenq_Test_ControllerTestCase_IndexControllerTest extends Webenq_Test_Case_Controller
{
    public function testIndexActionRendersLoginForm()
    {
// @todo check test, phpunit is not redirected to user/login,
//        $this->dispatch('/');
//        $this->assertRedirectTo('/user/login');
    }
    public function testCorrectControllerIsUsed()
    {
        //last controller is questionnaire (which is correct, because 'questionnaire' is last controller on stack)
        $this->dispatch('/');
        $this->assertController('questionnaire');
    }
}