<?php
/**
 * Webenq
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Tests_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests_Application
 */
class Webenq_Test_ControllerTestCase_UserControllerTest extends Webenq_Test_Case_Controller
{
    public function testCorrectControllerIsUsed()
    {
        $this->dispatch('/user/login');
        $this->assertController('user');
    }
//    public function testLoginFormIsRendered()
//    {
//        $this->dispatch('/user/login');
//        $this->assertAction('login');
//        $this->assertQuery('input#username');
//        $this->assertXpath("//input[@id = 'username']");

//    }

    public function testInvalidUserCannnotLogin()
    {
        $this->loadDatabase();

        $this->getRequest()->setMethod('POST')->setPost(array(
                'username' => 'invalidxyz',
                'password' => 'invalidxyz'
        ));
        $this->dispatch('user/login');
        $this->assertFalse(Zend_Auth::getInstance()->hasIdentity(), "should not be able to login as 'invalidxyz'");

    }
    public function testUserCanLoginAndLogout()
    {
        $this->loadDatabase();
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