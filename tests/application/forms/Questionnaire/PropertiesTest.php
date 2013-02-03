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
 * @package    Webenq_Tests_Questionnaires
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests_Questionnaires
 */
class Webenq_Test_Form_Questionnaire_Properties extends Webenq_Test_Case_Form
{
    public $setupDatabase = true;

    public function testFormValidationWithNoInputsShouldFail()
    {
        $this->getForm();

        $value = array();
        $this->assertFalse($this->_form->isValid($value));
    }

    public function testFormValidationWhenNoTitleIsSetShoudFail()
    {
        $this->getForm();

        $value = array('other' => array('en' => 'test', 'default_language' => 'en'));
        $this->assertFalse($this->_form->isValid($value));
    }

    public function testFormValidationWithTitleInDefaultLanguageIsOk()
    {
        $this->getForm();

        $value = array('title' => array('en' => 'test', 'default_language' => 'en'));
        $this->assertTrue($this->_form->isValid($value));
    }

    /*
     * Testing start and end dates
     */
    public function testStartDateBeforeEndDateIsOk()
    {
        $this->getForm();

        $value = array(
            'title' => array('en' => 'test', 'default_language' => 'en'),
            'date_start' => '2012-02-03',
            'date_end' => '2013-02-03',
        );
        $this->assertTrue($this->_form->isValid($value));
    }

    public function testStartDateEqualToEndDateIsOk()
    {
        $this->getForm();

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                        'date_start' => '2012-02-03',
                        'date_end' => '2012-02-03',
                );
                $this->assertTrue($this->_form->isValid($value));
    }

    public function testStartDateAfterEndDateShouldFail()
    {
        $this->getForm();

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                        'date_start' => '2013-02-03',
                        'date_end' => '2012-02-03',
                );
                $this->assertFalse($this->_form->isValid($value));
    }

    public function testStartDateAfterEndDateButBeforeInStringSortShouldFail()
    {
        $this->getForm();

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                        'date_start' => '2012-02-10',
                        'date_end' => '2012-02-9',
                );
                $this->assertFalse($this->_form->isValid($value));
    }

    public function testNoStartDateButEndDateIsOk()
    {
        $this->getForm();

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                'date_end' => '2012-02-03',
        );
        $this->assertTrue($this->_form->isValid($value));

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                'date_start' => '',
                'date_end' => '2012-02-03',
        );
        $this->assertTrue($this->_form->isValid($value));
    }

    public function testStartDateButNoEndDateIsOk()
    {
        $this->getForm();

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                'date_start' => '2012-02-03',
        );
        $this->assertTrue($this->_form->isValid($value));

        $value = array(
                'title' => array('en' => 'test', 'default_language' => 'en'),
                'date_start' => '2012-02-03',
                'date_end' => ''
        );
        $this->assertTrue($this->_form->isValid($value));
    }
}