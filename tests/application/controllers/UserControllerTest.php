<?php
class Webenq_Test_ControllerTestCase_UserControllerTest extends Webenq_Test_Case_Controller
{
    public function testLoginFormIsRendered()
    {
        $this->dispatch('user/login');
        $this->assertQuery('input#username');
        $this->assertQuery('input#password');
    }

    public function testUserCanLoginAndLogout()
    {
        $this->getRequest()->setMethod('POST')->setPost(array(
            'username' => 'test',
            'password' => 'test'
        ));
        $this->dispatch('user/login');
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());

        $this->dispatch('user/logout');
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
    }
}