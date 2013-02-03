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

/**
 * @package    WebEnq4_Tests
 */
class Webenq_Test_View_Helper_MlTextDefaultLanguageElement extends Webenq_Test_Case_Plugin
{
    public function testViewHelperAllFormElementsArePresent()
    {
        $element = new WebEnq4_View_Helper_MlTextDefaultLanguageElement();
        $element->setView(new Zend_View());

        $values = array();

        $attribs = array('languages' => array('en', 'nl'));

        $html = $element->mlTextDefaultLanguageElement('test', null, $attribs);

        /* text input fields */
        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'text',
                'value' => '',
                'name' => 'test[nl]',
                'id' => 'test-nl')), $html);

        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'text',
                'value' => '',
                'name' => 'test[en]',
                'id' => 'test-en')), $html);

        /* language selection radio */
        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'radio',
                'value' => 'en',
                'name' => 'test[default_language]',
                'id' => 'test-default_language-en')), $html);

        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'radio',
                'value' => 'nl',
                'name' => 'test[default_language]',
                'id' => 'test-default_language-nl')), $html);

        /* no default language selected */
        $this->assertNotTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'radio',
                'checked' => 'checked',
                'name' => 'test[default_language]')), $html);
    }

    public function testViewHelperSetsProperDefaultLanguage()
    {
        $element = new WebEnq4_View_Helper_MlTextDefaultLanguageElement();
        $element->setView(new Zend_View());

        $values = array('default_language' => 'en');

        $attribs = array('languages' => array('en', 'nl'));

        $html = $element->mlTextDefaultLanguageElement('test', $values, $attribs);

        /* language en selected */
        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'radio',
                'checked' => 'checked',
                'value' => 'en',
                'name' => 'test[default_language]',
                'id' => 'test-default_language-en')), $html);

        /* language nl not selected */
        $this->assertNotTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'radio',
                'checked' => 'checked',
                'value' => 'nl',
                'name' => 'test[default_language]',
                'id' => 'test-default_language-nl')), $html);
    }

    public function testViewHelperSetsInputStringsOk()
    {
        $element = new WebEnq4_View_Helper_MlTextDefaultLanguageElement();
        $element->setView(new Zend_View());

        $values = array('en' => 'English text', 'nl' => 'Nederlandse tekst');

        $attribs = array('languages' => array('en', 'nl'));

        $html = $element->mlTextDefaultLanguageElement('test', $values, $attribs);

        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'text',
                'value' => 'English text',
                'name' => 'test[en]',
                'id' => 'test-en')), $html);

        $this->assertTag(array('tag' => 'input', 'attributes' => array(
                'type' => 'text',
                'value' => 'Nederlandse tekst',
                'name' => 'test[nl]',
                'id' => 'test-nl')), $html);
    }
}