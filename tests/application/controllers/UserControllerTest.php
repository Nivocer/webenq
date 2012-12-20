<?php
class Webenq_Test_ControllerTestCase_UserControllerTest extends Webenq_Test_Case_Controller
{
    public function testLoginFormIsRendered()
    {
        $this->dispatch('/user/login');

        $this->assertController('user');
        $this->assertAction('login');
//        $this->assertQuery('input#username');
//        $this->assertQuery('input#password');
    }

    public function testUserCanLoginAndLogout()
    {
        $this->loadDatabase();

        $this->getRequest()->setMethod('POST')->setPost(array(
            'username' => 'invalidxyz',
            'password' => 'invalidxyz'
        ));
        $this->dispatch('user/login');
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity(), "should not be able to login as 'invalidxyz'");


        $this->getRequest()->setMethod('POST')->setPost(array(
                'username' => 'admin',
                'password' => 'webenq'
        ));

        $this->dispatch('user/login');
        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity(),"should be able to login 'admin'");

        $this->dispatch('user/logout');
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity(), "unable to logout");
    }
}