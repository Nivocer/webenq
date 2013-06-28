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
class Webenq_Test_Form_AnswerDomain_Tab_TextTest extends Webenq_Test_Case_Form
{
    /**
     * Test to check setDefaults/getValues based on database info
     * @dataProvider providerSetDEfaultsGetValuesWork
     */
    function testSetDefaultsGetValuesWork($case){
            $form=New Webenq_Form_AnswerDomain_Tab_Text();
            $form->setDefaults($case);
            $model=new Webenq_Model_AnswerDomainText();
            $model->fromArray($form->getValues());
            //formArray doesn't set identifier)
            $model->assignIdentifier($model->id);

            $dataForm=$model->toArray();
            //fixes for database (reset id's)
            $case['translation_id']=0;
            foreach ($case['Translation'] as $language=>$texts) {
                $case['Translation'][$language]['id']=0;
            }
            $this->assertArrayContainedIn($case, $dataForm);
        //}
    }
      public function providerSetDefaultsGetValuesWork(){
        return array(
                array (
                        0 =>
                        array (
                                'id' => 1,
                                'validators' => NULL,
                                'filters' => NULL,
                                'options' => NULL,
                                'answer_domain_item_id' => NULL,
                                'type' => 'AnswerDomainText',
                                'min_length' => NULL,
                                'max_length' => NULL,
                                'min' => NULL,
                                'max' => NULL,
                                'missing' => NULL,
                                'min_choices' => NULL,
                                'max_choices' => NULL,
                                'translation_id' => '1372424100671358',
                                'Translation' =>
                                array (
                                        'en' =>
                                        array (
                                                'id' => 1372424100671358,
                                                'name' => 'Text',
                                                'lang' => 'en',
                                        ),
                                        'nl' =>
                                        array (
                                                'id' => 1372424100671358,
                                                'name' => 'Tekst',
                                                'lang' => 'nl',
                                        ),
                                ),
                        ),
                )

            );
      }
}