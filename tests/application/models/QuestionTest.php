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
class Webenq_Test_Model_QuestionTest extends Webenq_Test_Case_Model_Question
{
    public function testAnswersCanBeAddedToQuestion()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('answer 1', 'answer 2'));
        $this->assertTrue(count($question->getAnswerValues()) == 2);
    }

    public function testMinimumAndMaximumLengthAndValueAreDetectedCorrectly()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('12', '123456789'));
        $this->assertTrue($question->minLen() == 2);
        $this->assertTrue($question->maxLen() == 9);
        $this->assertTrue($question->minVal() == 12);
        $this->assertTrue($question->maxVal() == 123456789);
    }

    public function testQuestionIsSearchable()
    {
        $result = Webenq_Model_Question::search('e', null, 1);
        $this->assertTrue($result instanceof Doctrine_Collection);
    }

    public function testQuestionIsAutocompletable()
    {
        $result = Webenq_Model_Question::autocomplete('e', null, 1);
        $this->assertTrue(is_array($result));
        if (is_array($result) && key_exists(0, $result)) {
            $this->assertTrue(is_array($result[0]));
            $this->assertTrue(key_exists('value', $result[0]));
            $this->assertTrue(key_exists('label', $result[0]));
        }
    }
}