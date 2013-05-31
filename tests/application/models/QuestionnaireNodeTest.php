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
class Webenq_Test_Model_QuestionnaireNodeTest extends Webenq_Test_Case_Model
{
    /**
     * @dataProvider casesSave
     */
    public function testFromThenToArrayShouldWork($expectedArray)
    {
        $node = new Webenq_Model_QuestionnaireNode();

        $node->fromArray($expectedArray);
        $actualArray = $node->toArray();

        $this->assertTrue(
            $this->arrayNestedElementsPresent($expectedArray, $actualArray));
    }

    /**
     * @dataProvider casesSave
     */
    public function testSaveWorks($expectedArray)
    {
        $this->createDatabase();

        $node = new Webenq_Model_QuestionnaireNode();
        $node->fromArray($expectedArray);
        $node->save();

        $actualArray = Doctrine_Core::getTable('Webenq_Model_QuestionnaireNode')
        ->find($node->id)
        ->toArray();

        $this->assertTrue(
            $this->arrayNestedElementsPresent($expectedArray, $actualArray));
    }

    public function casesSave() {
        return array(
                // minimal data
                array(0 => array()),
                array(1 => array(
                    'questionnaire_element_id'=> '1'
                )),

                // normal data
                array(2 => array(
                    'QuestionnaireElement'=>array(
                        'Translation' => array(
                            'en' => array('text' => 'English question'),
                            'nl' => array('text' => 'Dutch question'),
                        )
                    )
                )),
                array(3 => array(
                    'QuestionnaireElement'=>array(
                        'Translation' => array(
                            'en' => array('text' => 'English question'),
                            'nl' => array('text' => 'Dutch question'),
                        ),
                        'AnswerDomain' => array(
                            'Translation' => array(
                                'en' => array('name' => 'English answer'),
                                'nl' => array('name' => 'Dutch answer'),
                            )
                        )
                    )
                )),
                array(4 => array(
                    'QuestionnaireElement'=>array(
                        'Translation' => array(
                            'en' => array('text' => 'English question'),
                            'nl' => array('text' => 'Dutch question'),
                        ),
                        'AnswerDomain' => array(
                            'Translation' => array(
                                'en' => array('name' => 'English answer'),
                                'nl' => array('name' => 'Dutch answer'),
                            ),
                            'AnswerDomainItem' => array(
                                'Translation' => array(
                                    'en' => array('label' => 'English item'),
                                    'nl' => array('label' => 'Dutch item'),
                                ),
                            )
                        )
                    )
                )),

                /* choice element with items
                array(5 => array(
                    'QuestionnaireElement'=>array(
                        'Translation' => array(
                            'en' => array('text' => 'English question'),
                            'nl' => array('text' => 'Dutch question'),
                        ),
                        'AnswerDomain' => array(
                            'AnswerDomainItem' => array(
                                'Translation' => array(
                                    'en' => array('label' => 'English item'),
                                    'nl' => array('label' => 'Dutch item'),
                                ),
                                'items' => array(
                                    array(
                                        'Translation' => array(
                                            'en' => array('label' => 'English item'),
                                            'nl' => array('label' => 'Dutch item'),
                                        ),
                                    )
                                ),
                            ),
                        )
                    )
                )), */

        );
    }

}