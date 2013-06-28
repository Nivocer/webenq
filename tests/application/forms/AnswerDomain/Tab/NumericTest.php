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
class Webenq_Test_Form_AnswerDomain_Tab_NumericTest extends Webenq_Test_Case_Form
{
    /**
     * Test to check setDefaults/getValues based on database info
     * @dataProvider providerSetDefaultsGetValuesWork
     */
    function testSetDefaultsGetValuesWork(array $case){
            $form=New Webenq_Form_AnswerDomain_Tab_Numeric();
            $form->setDefaults($case);
            $model=new Webenq_Model_AnswerDomainNumeric();
            $model->fromArray($form->getValues());
            //formArray doesn't set identifier)
            $model->assignIdentifier($model->id);

            $dataForm=$model->toArray();
            //fixes for database (reset id's)
            $case['translation_id']=0;
            foreach ($case['Translation'] as $language=>$texts) {
                $case['Translation'][$language]['id']=0;
            }

            $this->assertEquals($case,$dataForm, "failure with answer domain id:".$case["id"]);
    }
    public function providerSetDefaultsGetValuesWork(){
        return array(
                array (
                  array (
                    'id' => 2,
                    'validators' => NULL,
                    'filters' => NULL,
                    'options' => NULL,
                    'answer_domain_item_id' => NULL,
                    'type' => 'AnswerDomainNumeric',
                    'min_length' => NULL,
                    'max_length' => NULL,
                    'min' => NULL,
                    'max' => NULL,
                    'missing' => NULL,
                    'min_choices' => NULL,
                    'max_choices' => NULL,
                    'translation_id' => '1372423561326499',
                    'Translation' =>
                    array (
                      'en' =>
                      array (
                        'id' => 1372423561326499,
                        'name' => 'Numeric',
                        'lang' => 'en',
                      ),
                      'nl' =>
                      array (
                        'id' => 1372423561326499,
                        'name' => 'Numeriek',
                        'lang' => 'nl',
                      ),
                    ),
                  ),
                ),
                array(
                  array (
                    'id' => 3,
                    'validators' => NULL,
                    'filters' => NULL,
                    'options' => NULL,
                    'answer_domain_item_id' => NULL,
                    'type' => 'AnswerDomainNumeric',
                    'min_length' => NULL,
                    'max_length' => NULL,
                    'min' => '0.0',
                    'max' => '110.0',
                    'missing' => NULL,
                    'min_choices' => NULL,
                    'max_choices' => NULL,
                    'translation_id' => '1372423560345972',
                    'Translation' =>
                    array (
                      'en' =>
                      array (
                        'id' => 1372423560345972,
                        'name' => 'Age',
                        'lang' => 'en',
                      ),
                      'nl' =>
                      array (
                        'id' => 1372423560345972,
                        'name' => 'Leeftijd',
                        'lang' => 'nl',
                      ),
                    ),
                  ),
                )
            );
    }
}