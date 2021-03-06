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
class Webenq_Test_Model_Question_Closed_Scale_FiveTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $this->loadDatabase();

        $question = Webenq_Model_Question::factory($data, 'nl');
    	//$this->assertTrue($question instanceof Webenq_Model_Question_Closed_Scale_Five);
        $this->assertTrue(in_array('Webenq_Model_Question_Closed_Scale_Five', $question->getValidTypes()));
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Closed_Scale_Five);
    }

    public function testCalculatedPercentagesAreCorrect()
    {
        $data = array(1,1,1,1,2,2,2,3,3,4,4,4,5,5,5,5);
    	$question = new Webenq_Model_Question_Closed_Scale_Five();
    	$question->setAnswerValues($data);

    	$actualResult = $question->getPercentages();
    	$expectedResult = array(1 => 25, 2 => 18.75, 3 => 12.5, 4 => 18.75, 5 => 25);
    	$this->assertTrue($actualResult == $expectedResult);
    	$this->assertTrue(array_sum($actualResult) == 100);

    	$actualResult = $question->getNegativeNeutralPositivePercentages();
    	$expectedResult = array(0 => 43.75, 1 => 12.5, 2 => 43.75);
    	$this->assertTrue($actualResult == $expectedResult);
    	$this->assertTrue(array_sum($actualResult) == 100);
    }
}