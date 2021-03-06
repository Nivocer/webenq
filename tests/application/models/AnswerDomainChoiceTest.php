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
class Webenq_Test_Model_AnswerDomainChoiceTest extends Webenq_Test_Case_Model
{
    /**
     * @dataProvider casesSave
     */
    public function testFromThenToArrayShouldWork($expectedArray)
    {
        $this->createDatabase();
        $answerdomain = new Webenq_Model_AnswerDomainChoice();

        $answerdomain->fromArray($expectedArray);
        $actualArray = $answerdomain->toArray();

        $this->assertArrayContainedIn($expectedArray, $actualArray);
    }

    /**
     * @dataProvider casesSave
     */
    public function testSaveWithItemsWorks($expectedArray)
    {
        $this->createDatabase();

        $answerdomain = new Webenq_Model_AnswerDomainChoice();

        $answerdomain->fromArray($expectedArray);
        $answerdomain->save();
        $answerdomain->refresh(true);

        /* we won't get a sortable field back from the database, but we do
         * need to reaarange what we expect
         */
        if (isset($expectedArray['items']['sortable'])) {
            $items = array();
            foreach ($expectedArray['items']['sortable'] as $i) {
                if (isset($expectedArray['items'][$i])) {
                    $items[] = $expectedArray['items'][$i];
                }
            }
            $expectedArray['items'] = $items;
        }

        $record = Doctrine_Core::getTable('Webenq_Model_AnswerDomainChoice')
        ->find($answerdomain->id);
        $actualArray = $record->toArray();

        $this->assertArrayContainedIn($expectedArray, $actualArray);
    }

    public function testSaveChangedPropertiesWorks()
    {
        /*
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
        */
    }

    public function testSaveChangesInSharedItemsShouldDuplicateItems()
    {
        /*
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
        */
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
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'AnswerDomainItem' => array(
                    'Translation' => array(
                        'en' => array('label' => 'Item list'),
                        'nl' => array('label' => 'Itemlijst'),
                    )
                )
            )),

            // with one item
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'AnswerDomainItem' => array(
                    'Translation' => array(
                        'en' => array('label' => 'Item list'),
                        'nl' => array('label' => 'Itemlijst'),
                    )
                ),
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Single item'
                        )
                    ),
                )
            )),

            // with one item but no AnswerDomainItem info
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Single item'
                        )
                    ),
                )
            )),

            // with multiple items
            array(array(
                    'type' => 'AnswerDomainChoice',
                    'Translation' => array(
                        'en' => array('name' => 'Choice domain'),
                        'nl' => array('name' => 'Keuzedomein'),
                    ),
                    'items' => array(
                        0 => array(
                            'value' => '1',
                            'isActive' => true,
                            'label' => array(
                                'en' => 'First item'
                            )
                        ),
                        1 => array(
                            'value' => '2',
                            'isActive' => true,
                            'label' => array(
                                'en' => 'Second item'
                            )
                        )
                    )
            )),

            // with multiple items and sortable but already sorted
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'items' => array(
                    'sortable' => array(0,1),
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'First item'
                        )
                    ),
                    1 => array(
                        'value' => '2',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Second item'
                        )
                    )
                )
            )),

            // with multiple items and sortable in different order
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'items' => array(
                    'sortable' => array(1, 0),
                    0 => array(
                        'value' => '2',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Second item'
                        )
                    ),
                    1 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'First item'
                        )
                    )
                )
            )),

            // with main item info, multiple items, new sorting
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'AnswerDomainItem' => array(
                    'Translation' => array(
                        'en' => array('label' => 'Item list'),
                        'nl' => array('label' => 'Itemlijst'),
                    )
                ),
                'items' => array(
                    'sortable' => array(2,0,1),
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Middle item'
                        )
                    ),
                    1 => array(
                        'value' => '2',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Last item'
                        )
                    ),
                    2 => array(
                        'value' => '0',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'First item'
                        )
                    ),
                )
            )),

        );
    }

    /**
     * @dataProvider casesSaveChanges
     */
    public function testSaveChangesInItemsShouldWork($expectedArray, $changesArray)
    {
        $this->createDatabase();

        $answerdomain = new Webenq_Model_AnswerDomainChoice();

        $answerdomain->fromArray($expectedArray);
        $answerdomain->save();
        $answerdomain->refresh(true);

        // overwrite with new items info
        $answerdomain->fromArray($changesArray);
        $answerdomain->save();
        $answerdomain->refresh(true);

        // now we expect to see the new items
        if (isset($changesArray['items'])) {
            $expectedArray['items'] = $changesArray['items'];
        }

        /* we won't get a sortable field back from the database, but we do
         * need to reaarange what we expect
        */
        if (isset($expectedArray['items']['sortable'])) {
            $items = array();
            foreach ($expectedArray['items']['sortable'] as $i) {
                if (isset($expectedArray['items'][$i])) {
                    $items[] = $expectedArray['items'][$i];
                }
            }
            $expectedArray['items'] = $items;
        }

        $record = Doctrine_Core::getTable('Webenq_Model_AnswerDomainChoice')
        ->find($answerdomain->id);
        $actualArray = $record->toArray();

        $this->assertArrayContainedIn($expectedArray, $actualArray);
    }

    /**
     * Test cases for saving changes
     */
    public function casesSaveChanges() {
        return array(
            // with one item
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'AnswerDomainItem' => array(
                    'Translation' => array(
                        'en' => array('label' => 'Item list'),
                        'nl' => array('label' => 'Itemlijst'),
                    )
                ),
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Single item'
                        )
                    ),
                )
            // only change the item label
            ), array(
                'items' => array(
                    0 => array(
                        'label' => array(
                            'en' => 'One item'
                        )
                    ),
                )
            )),

            // with one item
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'AnswerDomainItem' => array(
                    'Translation' => array(
                        'en' => array('label' => 'Item list'),
                        'nl' => array('label' => 'Itemlijst'),
                    )
                ),
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Single item'
                        )
                    ),
                )
            // change the item label and add an item
            ), array(
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'One item'
                        )
                    ),
                    1 => array(
                        'value' => '2',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Second item'
                        )
                    ),
                )
            )),

            // with multiple items
            array(array(
                'type' => 'AnswerDomainChoice',
                'Translation' => array(
                    'en' => array('name' => 'Choice domain'),
                    'nl' => array('name' => 'Keuzedomein'),
                ),
                'items' => array(
                    0 => array(
                        'value' => '1',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'First item'
                        )
                    ),
                    1 => array(
                        'value' => '2',
                        'isActive' => true,
                        'label' => array(
                            'en' => 'Second item'
                        )
                    )
                )
            ), array(
            )),

                // with multiple items and sortable but already sorted
                array(array(
                        'type' => 'AnswerDomainChoice',
                        'Translation' => array(
                                'en' => array('name' => 'Choice domain'),
                                'nl' => array('name' => 'Keuzedomein'),
                        ),
                        'items' => array(
                                'sortable' => array(0,1),
                                0 => array(
                                        'value' => '1',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'First item'
                                        )
                                ),
                                1 => array(
                                        'value' => '2',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'Second item'
                                        )
                                )
                        )
                ), array(
                )),

                // with multiple items and sortable in different order
                array(array(
                        'type' => 'AnswerDomainChoice',
                        'Translation' => array(
                                'en' => array('name' => 'Choice domain'),
                                'nl' => array('name' => 'Keuzedomein'),
                        ),
                        'items' => array(
                                'sortable' => array(1, 0),
                                0 => array(
                                        'value' => '2',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'Second item'
                                        )
                                ),
                                1 => array(
                                        'value' => '1',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'First item'
                                        )
                                )
                        )
                ), array(
                )),

                // with main item info, multiple items, new sorting
                array(array(
                        'type' => 'AnswerDomainChoice',
                        'Translation' => array(
                                'en' => array('name' => 'Choice domain'),
                                'nl' => array('name' => 'Keuzedomein'),
                        ),
                        'AnswerDomainItem' => array(
                                'Translation' => array(
                                        'en' => array('label' => 'Item list'),
                                        'nl' => array('label' => 'Itemlijst'),
                                )
                        ),
                        'items' => array(
                                'sortable' => array(2,0,1),
                                0 => array(
                                        'value' => '1',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'Middle item'
                                        )
                                ),
                                1 => array(
                                        'value' => '2',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'Last item'
                                        )
                                ),
                                2 => array(
                                        'value' => '0',
                                        'isActive' => true,
                                        'label' => array(
                                                'en' => 'First item'
                                        )
                                ),
                        )
                ), array(
                )),

        );
    }

}
