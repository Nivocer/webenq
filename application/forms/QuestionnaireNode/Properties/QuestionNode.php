<?php
/**
 * WebEnq4
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
 * @package    Webenq_Questionnaires_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 * @todo       merge initClasses to initTab($name);
 */

/**
 * Form to deal with question properties (text, answers, options).
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_QuestionnaireNode_Properties_QuestionNode extends Webenq_Form_QuestionnaireNode_Properties
{

    public $_subFormNames=array('question', 'answer', 'options');
    public $situations=array();

    /**
     * Dynamically adapt the form based on data provided (e.g. the list of
     * items for a choice domain, a different choice of answer domain, etc).
     *
     * @param array $data Typically questionnaireNode->toArray() or $this->getRequest()->getPost()
     * @see Webenq_Form_QuestionnaireNode_Properties::adapt()
     */
    public function adapt(array $data) {
        if (isset($data['QuestionnaireElement']['AnswerDomain']['type'])) {
            //data from database/model
            $this->_answerDomainType=$data['QuestionnaireElement']['AnswerDomain']['type'];
        } else {
            //post data
            if (isset($data['answer']['type'])) {
                $this->_answerDomainType = $data['answer']['type'];
            }

            $this->getSituations($data); //writes to $this->situations

            foreach ($this->situations as $situation) {
                switch ($situation) {
                    case 'differentAnswerDomainChosen':
                        $answerDomain = Doctrine_Core::getTable('Webenq_Model_AnswerDomain')
                            ->find($data['question']['answer_domain_id']);
                        $this->_answerDomainType=$answerDomain->type;
                        // override with new info
                        //@todo reset webenq_forms_Answerdomain_items->$_itemsAdded?
                        $data['answer'] = $answerDomain->toArray();
                        break;
                    case 'newAnswerDomainChosen':
                        $this->_answerDomainType=$data['question']['new'];
                        break;
                    case 'newAnswerDomainTypeChosen':
                        $this->_answerDomainType=$data['question']['new'];
                        break;
                    case 'newAnswerDomainSameTypeChosen':
                        $this->_answerDomainType=$data['question']['new'];//should be the same
                        break;
                    case 'newAndExistingAnswerDomainChoosen':
                        $this->_answerDomainType=$data['answer']['type'];
                        return;//exit the foreach?
                        break;
                }
            }
        }

        $this->init();

        //add item rows
        if ($this->_answerDomainType=='AnswerDomainChoice') {
            if (isset($data['QuestionnaireElement']['AnswerDomain'])) {
                $this->getSubForm('answer')->getSubForm('items')->addItemRows($data['QuestionnaireElement']['AnswerDomain']);
            } elseif (isset ($data['answer'])) {
                $this->getSubForm('answer')->getSubForm('items')->addItemRows($data['answer']);
            }
        }
    }

    /**
     * Set defaults for question properties form
     *
     * The provided $defaults should be similar to the output of toArray() on
     * a questionnaire node.
     *
     * <ul>
     * <li>['id'], ['type'], ['root_id'], ...: node attributes
     * <li>['QuestionnaireElement']: related questionnaire question element
     * <li>['QuestionnaireElement']['AnswerDomain']: answer domain related to the questionnaire question element
     * </ul>
     *
     * If no ['QuestionnaireElement'] sub array is available, existing values
     * for ['question'], ['answers'] and ['options'] will be preserved.
     *
     * @param array Array with data for a questionnaire node
     */
    public function setDefaults(array $defaults)
    {
        //@todo check set questionnaire id
        if (isset($defaults['Questionnaire'])) {
            $defaults['questionnaire_id']=$defaults['Questionnaire'][0]['id'];
        }

        if (isset($defaults['QuestionnaireElement'])) {
            /* question tab */
            $defaults['question'] = $defaults['QuestionnaireElement'];
            //if we don't have answerId or type get type on question, get type from AnswerDomain
            if (!isset($defaults['question']['answer_domain_id']) &&
                  !isset($defaults['question']['new'])&&
                  isset($defaults['QuestionnaireElement']['AnswerDomain']['type'])
                  ) {
                $defaults['question']['new']=$defaults['QuestionnaireElement']['AnswerDomain']['type'];
            }

            /* answer options tab */
            //pass info from answerDomain
            if (isset($defaults['QuestionnaireElement']['AnswerDomain'])) {
                $defaults['answer'] = $defaults['QuestionnaireElement']['AnswerDomain'];
            }
            // pass the answer domain settings to the answer tab as possible defaults
            if (isset($defaults['QuestionnaireElement']['options']['answerDomain'])){
                foreach ($defaults['QuestionnaireElement']['options']['answerDomain'] as $key=>$value) {
                    $defaults['answer'][$key] = $value;
                }
            }

            /* options tab */
            //get defaults from answerDomain
            if (isset($defaults['QuestionnaireElement']['AnswerDomain'])) {
                $defaults['options']=$defaults['QuestionnaireElement']['AnswerDomain'];
            }
            //override from options
            if (isset($defaults['QuestionnaireElement']['options'])){
                foreach ($defaults['QuestionnaireElement']['options'] as $key=> $value){
                    $defaults['options'][$key]=$value;
                }
            }
            //override from questionnaireElement @todo if we want to override these properties from QuestionnaireElement['options'] we should move this up
            if (isset($defaults['QuestionnaireElement']['active'])) {
                $defaults['options']['active'] = $defaults['QuestionnaireElement']['active'];
            }
            if (isset($defaults['QuestionnaireElement']['required'])) {
                $defaults['options']['required'] = $defaults['QuestionnaireElement']['required'];
            }

        }
        parent::setDefaults($defaults);

    }

    /**
     * Retrieve all form element values
     *
     * @param  bool $suppressArrayNotation
     * @return array
     */
    public function getValues($suppressArrayNotation = false) {
        $values = parent::getValues($suppressArrayNotation);

        if (isset($values['question']) && is_array($values['question'])) {
            $values['QuestionnaireElement'] = $values['question'];
        } else {
            $values['QuestionnaireElement'] = array();
        }

        if (isset($values['answer']) && is_array($values['answer'])) {
            $values['QuestionnaireElement']['AnswerDomain'] = $values['answer'];
        }
        // store active/required in questionnaireElement @todo: check do we want this or are questionnaireElement reusable?
        if (isset($values['options']) && is_array($values['options'])) {
            $values['QuestionnaireElement']['options'] = $values['options'];
            if (isset($values['options']['required'])) {
                $values['QuestionnaireElement']['required']=$values['options']['required'];
            }
            if (isset($values['options']['active'])) {
                $values['QuestionnaireElement']['active']=$values['options']['active'];
            }
        }


        return $values;
    }

    /**
     * Analyse the data (typically posted) and determine which situations are
     * present (e.g. different choice of answer domain type)
     *
     * @param array Data to analyse
     * @return array Situations that may need action before redisplay form
     */
    public function getSituations(array $data)
    {
        $this->situations=array();
        //change: other existing answer domain: mismatch question[answer_domain_id] and answers[id] tab
        if ($data['question']['answer_domain_id']<>'0' &&
            $data['question']['new']=='0' &&
            $data['answer']['id'] <>'0' &&
            $data['question']['answer_domain_id'] <> $data['answer']['id']
            ) {
            $this->situations[]='differentAnswerDomainChosen';
        }

        //change to a new answer domain: new type is chosen, existing one on answer tab
        if ($data['question']['new'] <>'0' &&
            $data['answer']['id'] <>'0' && $data['answer']['id'] <>null
            ) {
            $this->situations[]='newAnswerDomainChosen';
        }

        //change to new answer domain: other answerDomaintType choosen
        if ($data['question']['new'] <>'0' &&
            ($data ['answer']['id']=='0'|| $data['answer']['id']==null ) &&
            'AnswerDomain'.$data['question']['new']<>$data['answer']['type']
            ) {
            $this->situations[]='newAnswerDomainTypeChosen';
        }
        //change to new answer domain: same answerDomaintType choosen
        if($data['question']['new'] <>'0' &&
             $data['answer']['id'] =='0' &&
            'AnswerDomain'.$data['question']['new'] == $data['answer']['type']
                ){
            $this->situations[]='newAnswerDomainSameTypeChosen';
        }
        //answer_domain_id and new domain type choosen (is also a validation error)
        //@todo add test
        if ($data['question']['answer_domain_id']<>'0' &&
            $data['question']['answer_domain_id']<>'' &&
            $data['question']['new']<>'0' &&
            $data['question']['new']<>''
            ) {
            $this->situations[]='newAndExistingAnswerDomainChoosen';
        }

        $this->_submitInfo=$this->getSubmitButtonUsed($data);
        if ($this->_submitInfo['name']=='done' ) {
            $this->situations[]='doneButtonPressed';
        }

        //@todo submitbutton pressed


        return $this->situations;
    }

}
