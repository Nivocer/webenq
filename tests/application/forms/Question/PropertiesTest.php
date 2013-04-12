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
class Webenq_Test_Form_Question_PropertiesTest extends Webenq_Test_Case_Form
{
    /**
     * @dataProvider formActionTests
     */
    public function testAppropriateActionIsDetermined($data)
    {
        $this->loadDatabase();
        $form = new  Webenq_Form_Question_Properties();
        $form->setDefaults($data['post']);
        $action = $form->getSituations();
        sort($action);
        sort($data['action']);
        //@todo comparing sorted arrays, but is order of actions important?
        $this->assertEquals($data['action'], $action);
    }

    /**
     * Returns an array of test cases for form handling
     */
    public function formActionTests()
    {
        return array(
            /*
             * Subform "question"
             */
            // existing answer domain chosen; mismatch with id on answers tab
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => 3,
                        'new' => 0,
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 4
                    )
                ),
                //'action' => array('different_answer_domain_chosen')
                'action' => array('X')
            )),

            // new answer domain chosen; existing answer domain id on answer tab
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 4
                    )
                ),
                //'action' => array('new_answer_domain_type_chosen')
                'action' => array('X')
            )),

            // new answer domain chosen; existing answer domain item id on answer tab
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 0,
                        'items' => array(
                            'id' => 2
                        )
                    )
                ),
                //'action' => array('new_answer_domain_type_chosen')
                'action' => array('X')
            )),

            // new "choice" answer domain chosen; answers tab has new "choice" info
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 0,
                        'items' => array(
                            'id' => 0,
                            1 => array('label' => array('en' => 'Item 1'))
                        )
                    )
                ),
                //'action' => array()
                'action' => array('X')
            )),

            // new "choice" answer domain chosen; answers tab has new "choice" info
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 0,
                        'type' => 'AnswerDomainChoice'
                    )
                ),
                //'action' => array()
                'action' => array('X')
            )),

            // new "choice" answer domain chosen; answers tab has new "text" or "numeric" info
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'id' => 0,
                        // no items[] data, but no other definite way to know what type it is
                    )
                ),
                'action' => array('X')
            )),

            // new "choice" answer domain chosen; answers tab has "text" info
            array(array(
                'post' => array(
                    'question' => array(
                            'text' => array('en'=> 'Title'),
                            'answer_domain_id' => '',
                            'new' => 'Choice',
                            'next' => 'submit'
                    ),
                    'answers' => array(
                        'type' => 'AnswerDomainText'
                    )
                ),
                //'action' => array()
                'action' => array('X')
            )),

            // new "choice" answer domain chosen; answers tab has "numeric" info
            array(array(
                'post' => array(
                    'question' => array(
                        'text' => array('en'=> 'Title'),
                        'answer_domain_id' => '',
                        'new' => 'Choice',
                        'next' => 'submit'
                    ),
                    'answers' => array(
                        'type' => 'AnswerDomainNumeric'
                    )
                ),
                //'action' => array()
                'action' => array('X')
            )),

            /*
             * Subform "anwers"
             */

            /*
             * Subform "options"
             */

        );
    }
}