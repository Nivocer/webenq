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
 * @see WebEnq4_Validate_MlTextHasDefaultLanguageString
 */
require_once 'WebEnq4/Validate/MlTextHasDefaultLanguageString.php';

/**
 * @category   WebEnq4
 * @package    WebEnq4_Validate
 * @subpackage UnitTests
 * @group      WebEnq4_Validate
 */
class WebEnq4_Validate_MlTextHasDefaultLanguageStringTest extends PHPUnit_Framework_TestCase
{
    public function testNoInputIsOk()
    {
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertTrue($validator->isValid(null));
    }

    public function testEmptyArrayIsOk()
    {
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertTrue($validator->isValid(array()));
    }

    public function testNotAnArrayShouldFail()
    {
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertFalse($validator->isValid(3));
        $this->assertFalse($validator->isValid('3'));
    }

    public function testNonEmptyArrayWithoutDefaultLanguageShouldFail()
    {
        $input = array('en'=>'Text', 'nl'=>'Tekst');
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertFalse($validator->isValid($input));
    }

    public function testDefaultLanguageSetAndNoStringInThatLanguageShouldFail()
    {
        $input = array('default_language'=>'fr', 'en'=>'Text', 'nl'=>'Tekst');
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertFalse($validator->isValid($input));
    }

    public function testDefaultLanguageSetAndEmptyStringInThatLanguageShouldFail()
    {
        $input = array('default_language'=>'en', 'en'=>'');
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertFalse($validator->isValid($input));
    }

    public function testDefaultLanguageSetAndStringInThatLanguageIsValid()
    {
        $input = array('default_language'=>'en', 'en'=>'Text');
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertTrue($validator->isValid($input));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new WebEnq4_Validate_MlTextHasDefaultLanguageString();
        $this->assertEquals(array(), $validator->getMessages());
    }
}
