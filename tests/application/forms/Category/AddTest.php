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
class Webenq_Test_Form_Category_Add extends Webenq_Test_Case_Form
{
    public $setupDatabase = true;

    public function testCheckElementsExists(){
        $form = new Webenq_Form_Category_Add();
        foreach (Webenq_Language::getLanguages() as $language) {
            $this->assertNotNull($form->getElement($language));
        }
        $this->assertNotNull($form->getElement('active'));
        $this->assertNotNull($form->getElement('submit'));
        //$this->assertNull( $form->getElement('cancel'));

    }
/*
 * we like to use assertType, but that gives some fatal error
 */
    public function testCheckElementsHasCorrectType(){
        $form = new Webenq_Form_Category_Add();
        foreach (Webenq_Language::getLanguages() as $language) {
            $this->assertEquals('Zend_Form_Element_Text', $form->getElement($language)->getType());
        }
        $this->assertEquals('Zend_Form_Element_Checkbox', $form->getElement('active')->getType());
        $this->assertEquals('Zend_Form_Element_Submit', $form->getElement('submit')->getType());
    }

    public function testFormValidatesWhenOneCategoryTextIsProvided()
    {
        $form = new Webenq_Form_Category_Add();
        foreach (Webenq_Language::getLanguages() as $language) {
            $values=array('text'=>array($language => 'test'));
            $this->assertTrue($form->isValid($values));
        }
        //at least test english
        $values = array('text'=>array('en' => 'test'));
        $this->assertTrue($form->isValid($values));

        //and test twee categoryTexts
        $values = array('text'=>array('en' => 'test', 'nl'=>'test2'));
        $this->assertTrue($form->isValid($values));

        $this->assertFalse($form->isValid(array()));
        //and test some special strings
        $values = array('text'=>array('en' => ''));
        $this->assertFalse($form->isValid($values));

        //and test null
        $values = array('text'=>array('en' => null));
        $this->assertFalse($form->isValid($values));

        $values = array('text'=>array('en' => ' '));
        $this->assertFalse($form->isValid($values));

        $values = array('text'=>array('en' => "\t"));
        $this->assertFalse($form->isValid($values));

    }
}
