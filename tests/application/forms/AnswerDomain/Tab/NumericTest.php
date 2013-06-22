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
     * @todo use dataprovider
     */
    function testSetDefaultsGetValuesWork(){
        $this->loadDatabase();
        $cases=array();
        foreach (Webenq_Model_AnswerDomain::getAll() as $answerDomain){
            if ($answerDomain->type=='AnswerDomainNumeric'){
                $cases[]=$answerDomain->toArray();
            }
        }

        foreach ($cases as $case) {
        $this->loadDatabase();
            $form=New Webenq_Form_AnswerDomain_Tab_Numeric();
            $form->setDefaults($case);
            $model=new Webenq_Model_AnswerDomainNumeric();
            $model->fromArray($form->getValues());
            //formArray doesn't set identifier)
            $model->assignIdentifier($model->id);
            //add default answerDomainItem
            $model->AnswerDomainItem;
            $dataForm=$model->toArray();
            //fixes for database (reset id's)
            $case['translation_id']=0;
            foreach ($case['Translation'] as $language=>$texts) {
                $case['Translation'][$language]['id']=0;
            }
            $this->assertTrue(
                $this->arrayNestedElementsPresent($case, $dataForm), "failure with answer domain id:".$case["id"]);
        }
    }
}