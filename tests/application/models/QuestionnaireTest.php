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
class Webenq_Test_Model_QuestionnaireTest extends Webenq_Test_Case_Model
{
    /**
     * @dataProvider arrayTestCases
     */
    public function testFromThenToArrayShouldWork($expectedArray)
    {
        $questionnaire = new Webenq_Model_Questionnaire();

        $questionnaire->fromArray($expectedArray);
        $actualArray = $questionnaire->toArray();

        $this->assertTrue(
            $this->arrayNestedElementsPresent($expectedArray, $actualArray));
    }

    public function arrayTestCases() {
        return array(
            // minimal data
            array(0 => array('default_language' => 'az')),
            // normal data
            array(1 => array(
                'title'=>array(
                    'en' => 'English title',
                    'nl' => 'Dutch title',
                    'default_language' => 'en'
                )
            )),
            // try another (non-default) default_language
            array(2 => array(
                'title'=>array(
                    'en' => 'English title',
                    'nl' => 'Dutch title',
                    'default_language' => 'nl'
                )
            )),
        );
    }
}