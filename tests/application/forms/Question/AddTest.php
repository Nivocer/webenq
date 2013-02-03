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
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
class Webenq_Test_Form_Question_AddTest extends Webenq_Test_Case_Form
{
    public $setupDatabase = true;

    public function testOneLanguageIsRequired()
    {
        $form = $this->getForm();

        // invalid without languages
        $values = array('text' => array(
            'en' => '',
            'nl' => ''));
        $this->assertFalse($form->isValid($values));

        // valid with one language
        $values = array('text' => array(
            'en' => 'test',
            'nl' => ''));
        $this->assertTrue($form->isValid($values));

        // valid with all languages
        $values = array('text' => array(
            'en' => 'test',
            'nl' => 'test'));
        $this->assertTrue($form->isValid($values));
    }
}
