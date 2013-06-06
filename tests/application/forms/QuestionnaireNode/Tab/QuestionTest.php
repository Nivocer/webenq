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
class Webenq_Test_Form_QuestionnaireNode_Tab_QuestionTest extends Webenq_Test_Case_Form
{
//    public $setupDatabase = true;

    /**
     * @dataProvider formActionTests
     */
    public function testIsValid($data)
    {
        $this->loadDatabase();
        $form = $this->getForm();
        $form->setDefaults($data['post']);
        $isValid = $form->isValid($data['post']);
        $this->assertEquals($data['isValid'], $isValid);
    }

       public function formActionTests()
    {
        return array(
            /*
             * Subform "question"
             */
            //no question text in default language
            array(array(
                'post' => array(
                    'text' => array('en'=>''),
                    'answer_domain_id' => '',
                    'new' =>'',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
            //no reuse & new
            array(array(
                'post' => array(
                    'text' => array('nl'=> ''),
                    'answer_domain_id' => '',
                    'new' =>'new',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
            //no reuse & new
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '0',
                    'new' =>'',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
            //no reuse & new
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '',
                    'new' =>'0',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
            //no reuse & new
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '0',
                    'new' =>'0',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
            //reuse
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '1',
                    'new' =>'',
                    'next' => 'Next'
                ),
                'isValid' => true
            )),
            //new
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '',
                    'new' =>'AnswerDomainChoice',
                    'next' => 'Next'
                ),
                'isValid' => true
            )),
            //both reuse & new
            array(array(
                'post' => array(
                    'text' => array('en'=> 'Title'),
                    'answer_domain_id' => '1',
                    'new' =>'AnswerDomainChoice',
                    'next' => 'Next'
                ),
                'isValid' => false
            )),
        );
    }
}