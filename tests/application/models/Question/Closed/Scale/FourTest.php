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
class Webenq_Test_Model_Question_Closed_Scale_FourTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testQuestionTypeValidatesWhenProvidingValidAnswerValues($data)
    {
        $this->loadDatabase();

        $question = Webenq_Model_Question::factory($data, 'nl');
        $this->assertTrue(in_array('Webenq_Model_Question_Closed_Scale_Four',
            $question->getValidTypes()));
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testQuestionTypeDoesNotValidateWithInvalidAnswerValues($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse(in_array('Webenq_Model_Question_Closed_Scale_Four',
            $question->getValidTypes()));
    }
}