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
     * Create a node with an associated element of the given type
     *
     * @param string $type
     * @return Webenq_Model_QuestionnaireNode
     */
    protected function setUpNode($type)
    {
        $nodeClass = "Webenq_Model_Questionnaire${type}Node";
        $node = new $nodeClass();
        $node->type = "Questionnaire${type}Node";
        $node->QuestionnaireElement->type = "Questionnaire${type}Element";
        return $node;
    }

    /**
     * @dataProvider casesSave
     */
    public function testFromThenToArrayShouldWork($expectedArray)
    {
        $node = new Webenq_Model_QuestionnaireNode();

        $node->fromArray($expectedArray);
        $actualArray = $node->toArray();

        $this->assertArrayContainedIn($expectedArray, $actualArray);
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

        $this->assertArrayContainedIn($expectedArray, $actualArray);
    }

    public function testSaveChangedNodePropertiesWorks()
    {
        $this->createDatabase();

        $node1 = $this->setUpNode('Question');
        $node1->QuestionnaireElement->Translation->en->text = "Test first";
        $node1->save();

        $node2 = Doctrine_Core::getTable('Webenq_Model_QuestionnaireNode')
        ->find($node1->id);

        $node2->QuestionnaireElement->Translation->en->text = "Test second";
        $node2->save();

        $this->assertEquals($node1->id, $node2->id);

        $node1->refresh(true);

        $this->assertEquals($node1->QuestionnaireElement->Translation->en->text,
                $node2->QuestionnaireElement->Translation->en->text);
    }

    public function testSaveChangesInSharedElementShouldDuplicateElement()
    {
        $this->createDatabase();

        $node1 = $this->setUpNode('Question');
        $node1->QuestionnaireElement->Translation->en->text = "Test first";
        $node1->save();

        $node2 = $this->setUpNode('Question');
        $node2->QuestionnaireElement = $node1->QuestionnaireElement;
        $node2->save();

        $this->assertNotEquals($node1->id, $node2->id);
        $this->assertEquals($node1->QuestionnaireElement->id, $node2->QuestionnaireElement->id);

        $node2->QuestionnaireElement->Translation->en->text = "Test second";
        $node2->save();

        $this->assertNotEquals($node1->QuestionnaireElement->id, $node2->QuestionnaireElement->id);
    }

    /**
     * Test cases for saving
     */
    public function casesSave() {
        return array(
                // minimal data
                array(array()),

                // normal data
                array(array(
                    'QuestionnaireElement'=>array(
                        'Translation' => array(
                            'en' => array('text' => 'English question'),
                            'nl' => array('text' => 'Dutch question'),
                        )
                    )
                )),
                array(array(
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
                array(array(
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
