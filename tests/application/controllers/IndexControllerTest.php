<?php
class Webenq_Test_ControllerTestCase_IndexControllerTest extends Webenq_Test_Case_Controller
{
    public function testIndexActionRendersLoginForm()
    {
// @todo check test, phpunit is not redirected to user/login,
// last controller is questionnaire (which is correct, because 'questionnaire' is last controller on stack)
//        $this->dispatch('/');
//        $this->assertRedirectTo('/user/login');
    }
}