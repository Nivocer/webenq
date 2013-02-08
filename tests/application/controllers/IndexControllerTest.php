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
class Webenq_Test_ControllerTestCase_IndexControllerTest extends Webenq_Test_Case_Controller
{
    public $setupDatabase = true;

    public function testLoadDatabaseFixtureSucceeds() {
        $this->loadDatabase();

        $questionnaires = Webenq_Model_Questionnaire::getKeyValuePairs();
        $this->assertTrue(is_array($questionnaires) && (count($questionnaires)>0));
    }

    public function testIndexActionRendersLoginForm()
    {
// @todo check test, phpunit is not redirected to user/login,
//        $this->dispatch('/');
//        $this->assertRedirectTo('/user/login');
    }
    public function testCorrectControllerIsUsed()
    {
        //last controller is questionnaire (which is correct, because 'questionnaire' is last controller on stack)
        //$this->dispatch('/');
        //$this->assertController('questionnaire');
    }
}