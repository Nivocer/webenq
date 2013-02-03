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
 * @package    WebEnq4_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/** @see WebEnq4_Form_Element_MlTextDefaultLanguage */
require_once 'WebEnq4/Form/Element/MlTextDefaultLanguage.php';

/**
 * @package    WebEnq4_Tests
 */
class WebEnq4_Form_Element_MlTextDefaultLanguageTest extends PHPUnit_Framework_TestCase
{
    public function testNoInputForRequiredElementShouldFail()
    {
        $element = new WebEnq4_Form_Element_MlTextDefaultLanguage('item');
        $element->setRequired();

        $this->assertFalse($element->isValid(null));
        $this->assertTrue($element->hasErrors());
    }

    public function testEmptyInputForRequiredElementShouldFail()
    {
        $element = new WebEnq4_Form_Element_MlTextDefaultLanguage('item');
        $element->setRequired();

        $this->assertFalse($element->isValid(array()));
        $this->assertTrue($element->hasErrors());
    }

    public function testDefaultLanguageAndStringInThatLanguageIsOk()
    {
        $input = array('default_language' => 'en', 'en'=>'Text');
        $element = new WebEnq4_Form_Element_MlTextDefaultLanguage('item');
        $this->assertTrue($element->isValid($input));

        $element->setRequired();
        $this->assertTrue($element->isValid($input));
    }

}
