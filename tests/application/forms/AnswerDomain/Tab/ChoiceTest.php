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
class Webenq_Test_Form_AnswerDomain_Tab_ChoiceTest extends Webenq_Test_Case_Form
{
    /**
     * Test to check setDefaults/getValues based on database info
     * @dataProvider providerSetDefaultsGetValuesWork
     */
    function testSetDefaultsGetValuesWork($case)
    {
        $this->createDatabase();
        $form=New Webenq_Form_AnswerDomain_Tab_Choice();
        $form->getSubForm('items')->addItemRows($case);
        $form->setDefaults($case);

        $model=new Webenq_Model_AnswerDomainChoice();

        $model->fromArray($form->getValues($case));
        //formArray doesn't set identifier)
        $model->assignIdentifier($model->id);
        //add default answerDomainItem
        $model->AnswerDomainItem;

        $dataForm=$model->toArray();
        //fixes for $dataForm
        unset($dataForm['items']['new']);
        unset($dataForm['items']['sortable']);
        foreach ($dataForm['items'] as &$tempItem2) {
            unset($tempItem2['label']);
        }


        //fixes for database (reset id's)
        $case['translation_id']=0;
        foreach ($case['Translation'] as $language=>$texts) {
            $case['Translation'][$language]['id']=0;
        }
        if ($case['AnswerDomainItem']['id']==$case['AnswerDomainItem']['root_id']) {
            $case['AnswerDomainItem']['value']=null;
        }
        $case['AnswerDomainItem']['id']=null;
        $case['AnswerDomainItem']['lft']=null;
        $case['AnswerDomainItem']['rgt']=null;
        $case['AnswerDomainItem']['root_id']=null;
        $case['AnswerDomainItem']['translation_id']=0;
        $case['AnswerDomainItem']['level']=null;

        foreach ($case['items'] as &$tempItem) {
            unset($tempItem['lft']);
            unset($tempItem['rgt']);
            unset($tempItem['root_id']);
            unset($tempItem['translation_id']);
            unset($tempItem['level']);
            foreach ($tempItem['Translation'] as &$translation) {
                unset($translation['id']);
            }


        }

        //@todo check Translation['name'] in case['answerDomainItem']
        //
        //$dataForm['Translation']=$dataForm['label'];
        //var_dump(__FILE__,  __LINE__,$case,$dataForm);
        //$this->assertArrayContainedIn($case, $dataForm);
        $this->assertEquals($case, $dataForm, "failure with answer domain id:".$case["id"]);
    }
    public function providerSetDefaultsGetValuesWork()
    {
        return array(
array (
  0 =>
  array (
    'id' => 4,
    'validators' => NULL,
    'filters' => NULL,
    'options' => NULL,
    'answer_domain_item_id' => '3',
    'type' => 'AnswerDomainChoice',
    'min_length' => NULL,
    'max_length' => NULL,
    'min' => NULL,
    'max' => NULL,
    'missing' => NULL,
    'min_choices' => NULL,
    'max_choices' => NULL,
    'translation_id' => '1372425340633428',
    'Translation' =>
    array (
      'en' =>
      array (
        'id' => 1372425340633428,
        'name' => 'Yes/no/don\'t know',
        'lang' => 'en',
      ),
      'nl' =>
      array (
        'id' => 1372425340633428,
        'name' => 'Ja/nee/weet niet',
        'lang' => 'nl',
      ),
    ),
    'AnswerDomainItem' =>
    array (
      'id' => '3',
      'value' => 'yesno',
      'isNullValue' => null,
      'isActive' => true,
      'isHidden' => null,
      'translation_id' => '1372425339796024',
      'root_id' => '3',
      'lft' => '1',
      'rgt' => '8',
      'level' => '0',
      'Translation' =>
      array (
      ),
    ),
    'items' =>
    array (
      0 =>
      array (
        'id' => '6',
        'value' => 'yes',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339878440',
        'root_id' => '3',
        'lft' => '2',
        'rgt' => '3',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339878440,
            'label' => 'Yes',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339878440,
            'label' => 'Ja',
            'lang' => 'nl',
          ),
        ),
      ),
      1 =>
      array (
        'id' => '5',
        'value' => 'no',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339853486',
        'root_id' => '3',
        'lft' => '4',
        'rgt' => '5',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339853486,
            'label' => 'No',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339853486,
            'label' => 'Nee',
            'lang' => 'nl',
          ),
        ),
      ),
      2 =>
      array (
        'id' => '4',
        'value' => 'dontknow',
        'isNullValue' => true,
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339828544',
        'root_id' => '3',
        'lft' => '6',
        'rgt' => '7',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339828544,
            'label' => 'Don\'t know',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339828544,
            'label' => 'Weet niet',
            'lang' => 'nl',
          ),
        ),
      ),
    ),
  ),
  ),
array(
  1 =>
  array (
    'id' => 5,
    'validators' => NULL,
    'filters' => NULL,
    'options' => NULL,
    'answer_domain_item_id' => '1',
    'type' => 'AnswerDomainChoice',
    'min_length' => NULL,
    'max_length' => NULL,
    'min' => NULL,
    'max' => NULL,
    'missing' => NULL,
    'min_choices' => NULL,
    'max_choices' => NULL,
    'translation_id' => '1372425339197655',
    'Translation' =>
    array (
      'en' =>
      array (
        'id' => 1372425339197655,
        'name' => '5-point "(dis)agree"',
        'lang' => 'en',
      ),
      'nl' =>
      array (
        'id' => 1372425339197655,
        'name' => '5-punts "mee (on)eens"',
        'lang' => 'nl',
      ),
    ),
    'AnswerDomainItem' =>
    array (
      'id' => '1',
      'value' => 'scale5',
      'isNullValue' => null,
      'isActive' => true,
      'isHidden' => null,
      'translation_id' => '1372425339198891',
      'root_id' => '1',
      'lft' => '1',
      'rgt' => '12',
      'level' => '0',
      'Translation' =>
      array (
      ),
    ),
    'items' =>
    array (
      0 =>
      array (
        'id' => '13',
        'value' => '1',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425340080021',
        'root_id' => '1',
        'lft' => '2',
        'rgt' => '3',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425340080021,
            'label' => 'Strongly disagree',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425340080021,
            'label' => 'Helemaal mee oneens',
            'lang' => 'nl',
          ),
        ),
      ),
      1 =>
      array (
        'id' => '12',
        'value' => '2',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425340054952',
        'root_id' => '1',
        'lft' => '4',
        'rgt' => '5',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425340054952,
            'label' => 'Disagree',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425340054952,
            'label' => 'Mee oneens',
            'lang' => 'nl',
          ),
        ),
      ),
      2 =>
      array (
        'id' => '11',
        'value' => '3',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425340029847',
        'root_id' => '1',
        'lft' => '6',
        'rgt' => '7',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425340029847,
            'label' => 'Neutral',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425340029847,
            'label' => 'Neutraal',
            'lang' => 'nl',
          ),
        ),
      ),
      3 =>
      array (
        'id' => '10',
        'value' => '4',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425340004524',
        'root_id' => '1',
        'lft' => '8',
        'rgt' => '9',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425340004524,
            'label' => 'Agree',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425340004524,
            'label' => 'Mee eens',
            'lang' => 'nl',
          ),
        ),
      ),
      4 =>
      array (
        'id' => '9',
        'value' => '5',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339979379',
        'root_id' => '1',
        'lft' => '10',
        'rgt' => '11',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339979379,
            'label' => 'Strongly agree',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339979379,
            'label' => 'Helemaal mee eens',
            'lang' => 'nl',
          ),
        ),
      ),
    ),
  ),
  ),
array(
  2 =>
  array (
    'id' => 6,
    'validators' => NULL,
    'filters' => NULL,
    'options' => NULL,
    'answer_domain_item_id' => '2',
    'type' => 'AnswerDomainChoice',
    'min_length' => NULL,
    'max_length' => NULL,
    'min' => NULL,
    'max' => NULL,
    'missing' => NULL,
    'min_choices' => NULL,
    'max_choices' => NULL,
    'translation_id' => '1372425339396852',
    'Translation' =>
    array (
      'en' =>
      array (
        'id' => 1372425339396852,
        'name' => 'Gender',
        'lang' => 'en',
      ),
      'nl' =>
      array (
        'id' => 1372425339396852,
        'name' => 'Geslacht',
        'lang' => 'nl',
      ),
    ),
    'AnswerDomainItem' =>
    array (
      'id' => '2',
      'value' => 'gender',
      'isNullValue' => null,
      'isActive' => true,
      'isHidden' => null,
      'translation_id' => '1372425339398158',
      'root_id' => '2',
      'lft' => '1',
      'rgt' => '6',
      'level' => '0',
      'Translation' =>
      array (
      ),
    ),
    'items' =>
    array (
      0 =>
      array (
        'id' => '8',
        'value' => '1',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339941234',
        'root_id' => '2',
        'lft' => '2',
        'rgt' => '3',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339941234,
            'label' => 'Female',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339941234,
            'label' => 'Vrouw',
            'lang' => 'nl',
          ),
        ),
      ),
      1 =>
      array (
        'id' => '7',
        'value' => '2',
        'isNullValue' => '0',
        'isActive' => true,
        'isHidden' =>'0',
        'translation_id' => '1372425339916430',
        'root_id' => '2',
        'lft' => '4',
        'rgt' => '5',
        'level' => '1',
        'Translation' =>
        array (
          'en' =>
          array (
            'id' => 1372425339916430,
            'label' => 'Male',
            'lang' => 'en',
          ),
          'nl' =>
          array (
            'id' => 1372425339916430,
            'label' => 'Man',
            'lang' => 'nl',
          ),
        ),
      ),
    ),
  ),
)            );
    }
}