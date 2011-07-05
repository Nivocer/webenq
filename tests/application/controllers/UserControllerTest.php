<?php

class Webenq_Test_ControllerTestCase_UserControllerTest extends Webenq_Test_Controller
{
    public function testLoginFormIsRendered()
    {
        $this->dispatch('user/login');
        $this->assertQuery('input#username');
        $this->assertQuery('input#password');
    }

//    public function testUserCanLoginAndLogout()
//    {
//        $this->request->setMethod('POST');
//        $this->request->setParams(array(
//            'username' => 'test',
//            'password' => 'test'
//        ));
//        $this->dispatch('user/login');
//        $this->assertTrue(Zend_Auth::getInstance()->hasIdentity());
//        $this->assertTrue(Zend_Auth::getInstance()->getIdentity() == 'test');
//
//        $this->dispatch('user/logout');
//        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity());
//        $this->assertFalse(Zend_Auth::getInstance()->getIdentity() == 'test');
//    }
}