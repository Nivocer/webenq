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
class Webenq_Test_Model_Question_Open_DateTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Date);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Open_Date);
    }

    public function testDateToTimestampIsConvertedCorrectly()
    {
        $expected = strtotime('1981-02-22');
        $converted = Webenq_Model_Question_Open_Date::toTimestamp('1981-02-22');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('1981-22-02');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('02-22-1981');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('22-02-1981');
        $this->assertTrue($converted == $expected);
    }

    public function testTimestampToDateIsConvertedCorrectly()
    {
        $expected = '1981-02-22';
        $converted = Webenq_Model_Question_Open_Date::toFormat('1981-02-22', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('1981-22-02', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('02-22-1981', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('22-02-1981', 'Y-m-d');
        $this->assertTrue($converted == $expected);
    }

    public function testValidFormatsAreDefined()
    {
        $formats = Webenq_Model_Question_Open_Date::getValidFormats();
        $this->assertTrue(is_array($formats));
        $this->assertTrue(count($formats) > 0);
    }
}