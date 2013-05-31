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
  */

/**
 * Form to deal with question properties (text, answers, options).
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Question_Properties extends WebEnq4_Form
{
    /**
     * Type of answer domain: text, numeric, choice
     *
     * @var string
     */
    public $_answerDomainType;
    public $_defaultLanguage;
    private $_answerTypeSpecificForms=array(
        'Webenq_Form_AnswerDomain_Tab',
        'Webenq_Form_Question_Tab_Options'
    );

    /**
     * Initialises the form, sets the answer domain type
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options) && isset($options['answerDomainType'])) {
            $this->_answerDomainType=$options['answerDomainType'];
        }
        if (is_array($options) && isset($options['defaultLanguage'])) {
            $this->_defaultLanguage=$options['defaultLanguage'];
        }
        parent::__construct();
    }

    public function init()
    {
        $qid=new Zend_Form_Element_Hidden('questionnaire_id');
        $qid->removeDecorator('DtDdWrapper');
        $qid->removeDecorator('Label');
        $this->addElement($qid);

        $parentId = new Zend_Form_Element_Hidden('parent_id');
        $parentId->removeDecorator('DtDdWrapper');
        $parentId->removeDecorator('Label');
        $this->addElement($parentId);
    }

    public function _initDetermineFormName($tabName){
        switch ($tabName){
            case 'answer':
                $formName='Webenq_Form_AnswerDomain_Tab';
                break;
            default:
                $formName='Webenq_Form_Question_Tab_'.ucfirst($tabName);
                break;
        }
        //add answerdomain specific extension if neccessary
        if (in_array($formName, $this->_answerTypeSpecificForms)){
            if (in_array($this->_answerDomainType, array('AnswerDomainChoice', 'AnswerDomainNumeric', 'AnswerDomainText'))) {
                $formName.='_'.substr($this->_answerDomainType, 12);
            }
        }
    return $formName;
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
        /* translate from database data? */
        if (isset($defaults['QuestionnaireElement'])) {
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
            if (isset($defaults['QuestionnaireElement']['options']['options'])){
                foreach ($defaults['QuestionnaireElement']['options']['options'] as $key=> $value){
                    $defaults['options'][$key]=$value;
                }
            }
            //override from questionnaireElement
            $defaults['options']['active'] = $defaults['QuestionnaireElement']['active'];
            $defaults['options']['required'] = $defaults['QuestionnaireElement']['required'];
        }
        if (isset($defaults['Questionnaire'])) {
            $defaults['questionnaire_id']=$defaults['Questionnaire'][0]['id'];
        }
        parent::setDefaults($defaults);
    }


    /**
     * @return array: array with situations that needs action before redisplay form
     */
    public function getSituations()
    {
        $situations=array();
        $submitInfo=$this->getSubmitButtonUsed(array('next', 'previous','done'));
        //is subform valid
        //@todo check if we don't need to feed $formData[$tab]
        //@todo isValid: only one of question[reuse]/question[new] gebruiken

        switch ($submitInfo['subForm']){
            case 'question':
                //change: other existing answer domain: mismatch $question[answer_domain_id] and answers[id] tab
                if ($this->question->answer_domain_id->getValue()<>'0' &&
                    $this->question->new->getValue()=='0' &&
                    $this->answer->id->getValue() <>'0' &&
                    $this->question->answer_domain_id->getValue() <>$this->answer->id->getValue()){
                    $situations[]='differentAnswerDomainChosen';
                }

                //change to a new answer domain: new type is chosen, existing one on answer tab
                if ($this->question->new->getValue()<>'0' &&
                    $this->answer->id->getValue() <>'0' && $this->answer->id->getValue()<>null
                    ){
                    $situations[]='newAnswerDomainChosen';
                }

                //change to new answer domain: other answerDomaintType choosen
                if ($this->question->new->getValue() <>'0' &&
                    ( $this->answer->id->getValue()=='0'|| $this->answer->id->getValue()==null ) &&
                    'AnswerDomain'.$this->question->new->getValue()<>$this->answer->type->getValue()
                        ){
                    $situations[]='newAnswerDomainTypeChosen';
                }

                //change to new answer domain: same answerDomaintType choosen
                if($this->question->new->getValue()<>'0' &&
                     $this->answer->id->getValue()=='0' &&
                    'AnswerDomain'.$this->question->new->getValue()==$this->answer->type->getValue()
                        ){
                    $situations[]='newAnswerDomainSameTypeChosen';
                }

                break;
            case 'answersoptions':
            case 'options':
                break;
        }
        return $situations;
    }
}
