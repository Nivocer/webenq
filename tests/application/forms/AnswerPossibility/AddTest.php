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
class Webenq_Test_Form_AnswerPossibility_AddTest extends Webenq_Test_Case_Form_AnswerPossibilityGroup
{
    protected $_form;

    public function setUp()
    {
        parent::setUp();

        $answerPossibilityGroup = new Webenq_Model_AnswerPossibilityGroup();
        $answerPossibilityGroup->id = 1;
        $language = 'nl';
        $this->_form = new Webenq_Form_AnswerPossibility_Add($answerPossibilityGroup, $language);
    }

    public function testTextIsRequired()
    {
        $values = array('text' => '');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->text));
    }

    public function testValueIsRequiredAndMustBeInteger()
    {
        $values = array('value' => '');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->value));

        $values = array('value' => 'test');
        $this->_form->isValid($values);
        $this->assertTrue($this->hasErrors($this->_form->value));

        $values = array('value' => '1');
        $this->_form->isValid($values);
        $this->assertFalse($this->hasErrors($this->_form->value));
    }
}