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
 * @category   WebEnq4
 * @package    WebEnq4_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @see WebEnq4_Validate_DateTimePicker
 */
require_once 'WebEnq4/Validate/DateTimePicker.php';

/**
 * @category   WebEnq4
 * @package    WebEnq4_Validate
 * @subpackage UnitTests
 * @group      WebEnq4_Validate
 */
class WebEnq4_Validate_DateTimePickerTest extends PHPUnit_Framework_TestCase
{
    public function testNoInputIsOk()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertTrue($validator->isValid(null));
    }

    public function testEmptyInputIsOk()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertTrue($validator->isValid(''));
    }

    public function testZeroInputIsOk()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertTrue($validator->isValid('0000-00-00'));
    }

    public function testGarbageInputShouldFail()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertFalse($validator->isValid('this is not a date'));
    }

    public function testNonExistingDayOfMonthShouldFail()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertFalse($validator->isValid('2012-1-0'));

        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertFalse($validator->isValid('2012-12-33'));
    }
    public function testInLeapYearFebruary29IsOk()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertTrue($validator->isValid('2012-2-29'));
    }
    public function testInNonLeapYearFebruary29ShouldFail()
    {
        $validator = new WebEnq4_Validate_DateTimePicker();
        $this->assertFalse($validator->isValid('2013-2-29'));
    }
}
