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
class Webenq_Test_Model_Question_Open_NumberTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Number);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Open_Number);
    }

    public function testCalculatedSumOfValuesIsCorrect()
    {
        $data = array(1,1,1,1,2,2,2,3,3,4,4,4,5,5,5,5);
    	$question = new Webenq_Model_Question_Open_Number();
    	$question->setAnswerValues($data);

    	$actualResult = $question->sum();
    	$expectedResult = 48;
    	$this->assertTrue($actualResult == $expectedResult);
    }

    public function testCalculatedSumOfStringsIsZero()
    {
        $data = array('test', 'test');
    	$question = new Webenq_Model_Question_Open_Number();
    	$question->setAnswerValues($data);

    	$actualResult = $question->sum();
    	$expectedResult = 0;
    	$this->assertTrue($actualResult == $expectedResult);
    }

    public function testPeriodsAndCommasAreInterpretedCorrectly()
    {
        $validNumbers = array('1.0', '2,1', '2.100', '3,514.00', '+50', -50, 3.5, '-50.578,12');
        $question = Webenq_Model_Question::factory($validNumbers, 'nl');
        $this->assertTrue($question instanceof Webenq_Model_Question_Open_Number);
    }
}