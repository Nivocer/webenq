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
class Webenq_Test_Form_QuestionnaireNode_Tab_ChoiceTest extends Webenq_Test_Case_Form
{
    /**
     * Test to check setDefaults/getValues based on database info
     * @dataProvider getSetDefaults
     */
    function testSetDefaultsGetValuesWork($case){
        $form=New Webenq_Form_QuestionnaireNode_Tab_Options_Choice();
        $form->setDefaults($case);
        $this->assertEquals($case, $form->getValues());
    }
    /**
    * Test to check if valid array is returned
     * @dataProvider getSetDefaults
     */
    function testIsValidGetValuesWork($case){
        $form=New Webenq_Form_QuestionnaireNode_Tab_Options_Choice();
        $this->assertEquals($case, $form->getValidValues($case));
    }
    public function getSetDefaults(){
        return array(
            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'pulldown',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'checkbox',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'textComplete',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'input',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'slider',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'1',
            )),
            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '10',
                'presentationHeight' => '',
                'required' => '0',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),

            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '1',
                'active' => '0',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '1',
                'numberOfAnswers'=>'0',
            )),
            array(array(
                'presentation' => 'radio',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '1',
                'numberOfAnswers'=>'1',
            )),

            array(array(
                'presentation' => 'slider',
                'presentationWidth' => '10',
                'presentationHeight' => '2',
                'required' => '0',
                'active' => '1',
                'numberOfAnswers'=>'0',
            )),
        );
    }
}
