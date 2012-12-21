<?php
<?php
/**
 * WebEnq4 Library
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
 * @category   Webenq
 * @package    Webenq_Forms
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

class Webenq_Test_Form_User_LoginTest extends Webenq_Test_Case_Form
{
    public function testFormOnlyValidatesWhenBothUsernameAndPasswordAreProvided()
    {
        $form = new Webenq_Form_User_Login();

        $values = array('username' => 'test', 'password' => '');
        $this->assertFalse($form->isValid($values));

        $values = array('username' => '', 'password' => 'test');
        $this->assertFalse($form->isValid($values));

        $values = array('username' => 'test', 'password' => 'test');
        $this->assertTrue($form->isValid($values));
    }
}