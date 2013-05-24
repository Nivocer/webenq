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
    public $answerDomainType;
    public $_nodeType;
    public $_defaultLanguage;
    public $_classAnswers;
    public $_classOptions;

    /**
     * Initialises the form, sets the answer domain type
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options) && isset($options['answerDomainType'])) {
            $this->answerDomainType=$options['answerDomainType'];
        }
        if (is_array($options) && isset($options['defaultLanguage'])) {
            $this->_defaultLanguage=$options['defaultLanguage'];
        }
        if (is_array($options) && isset($options['nodeType'])) {
            $this->_nodeType=$options['nodeType'];
        }
        parent::__construct();
    }

    public function init()
    {
        $this->initDeterminClasses();
        switch ($this->_nodeType) {
            case 'QuestionnaireQuestionNode':
            case 'QuestionnaireTextNode':
                $this->initQuestionTab();
                $this->initAnswersTab();
                $this->initOptionsTab();
                break;
            case 'QuestionnaireGroupNode':
            case 'QuestionnaireLikertNode':
                $this->initGroupTab();
                $this->initQuestionsTab();
                $this->initAnswersTab();
                $this->initOptionsTab();

                    break;
            default:
                var_dump(__FILE__,  __LINE__,$this->nodeType);
        }
    }

    public function initDeterminClasses(){
        /* determine appropriate tab forms for answer settings and options */
        $answerDomainType = $this->answerDomainType;

        if (in_array($answerDomainType, array('AnswerDomainChoice', 'AnswerDomainNumeric', 'AnswerDomainText'))) {
            $this->_classAnswers = 'Webenq_Form_AnswerDomain_Tab_' . substr($answerDomainType, 12);
            $this->_classOptions = 'Webenq_Form_Question_Tab_Options_' . substr($answerDomainType, 12);
        } else {
            $this->_classAnswers = 'Webenq_Form_AnswerDomain_Tab';
            $this->_classOptions = 'Webenq_Form_Question_Tab_Options';
        }
    }
    public function initGroupTab(){
        $group=new Zend_Form_SubForm();
        $this->addSubForm($group, 'group');
        $this->getSubForm('group')->addDecorator('SubFormInTab');
    }
    public function initQuestionsTab(){
        $questions=new Zend_Form_SubForm();
        $this->addSubForm($questions, 'questions');
        $this->getSubForm('questions')->addDecorator('SubFormInTab');
    }
    public function initQuestionTab(){
    /* question text and type tab */
        $question = new Webenq_Form_Question_Tab_Question(array('defaultLanguage'=>$this->_defaultLanguage));
        $question->removeDecorator('DtDdWrapper');
        //$question->removeDecorator('Form');
        $this->addSubForm($question, 'question');
        $this->getSubForm('question')->addDecorator('SubFormInTab');
    }
    public function initAnswersTab(){
        /* answer domain settings tab */
        $answers = new $this->_classAnswers(array('defaultLanguage'=>$this->_defaultLanguage));
        $answers->removeDecorator('DtDdWrapper');
        $this->addSubForm($answers, 'answers');
        $this->getSubForm('answers')->addDecorator('SubFormInTab');

}
    public function initOptionsTab(){
    /* question options settings tab */
        $options = new $this->_classOptions(array('defaultLanguage'=>$this->_defaultLanguage));
        $options->removeDecorator('DtDdWrapper');
        $this->addSubForm($options, 'options');
        $this->getSubForm('options')->addDecorator('SubFormInTab');
}
    /**
     * Set defaults for all elements
     */
    public function setDefaults(array $defaults)
    {
        //question tab
        if (isset($defaults['Questionnaire'][0]['id'])){
            $defaults['question']['questionnaire_id']=$defaults['Questionnaire'][0]['id'];
        }
        if (isset($defaults['id'])){
            $defaults['question']['node_id']= $defaults['id'];
        }
        if (isset($defaults['QuestionnaireElement'])) {
            $defaults['question'] = $defaults['QuestionnaireElement'];
            //answer options tab
            //pass info from answerDomain
            if (isset($defaults['QuestionnaireElement']['AnswerDomain'])) {
                $defaults['answers'] = $defaults['QuestionnaireElement']['AnswerDomain'];
            }
            // pass the answer domain settings to the options tab as possible defaults
            if (isset($defaults['QuestionnaireElement']['options']['answerDomain'])){
                foreach ($defaults['QuestionnaireElement']['options']['answerDomain'] as $key=>$value) {
                    $defaults['answers'][$key] = $value;
                }
            }

            //options tab
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
        parent::setDefaults($defaults);
    }


    /**
     * @return array: array with situations that needs action before redisplay form
     */
    public function getSituations() {
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
                    $this->answers->id->getValue() <>'0' &&
                    $this->question->answer_domain_id->getValue() <>$this->answers->id->getValue()){
                    $situations[]='differentAnswerDomainChosen';
                }

                //change to a new answer domain: new type is chosen, existing one on answer tab

                if ($this->question->new->getValue()<>'0' &&
                    $this->answers->id->getValue() <>'0' && $this->answers->id->getValue()<>null
                    ){
                    $situations[]='newAnswerDomainChosen';
                }
                //change to new answer domain: other answerDomaintType choosen
                if ($this->question->new->getValue() <>'0' &&
                    ( $this->answers->id->getValue()=='0'|| $this->answers->id->getValue()==null ) &&
                    'AnswerDomain'.$this->question->new->getValue()<>$this->answers->type->getValue()
                        ){
                    $situations[]='newAnswerDomainTypeChosen';
                }
                //change to new answer domain: same answerDomaintType choosen
                if($this->question->new->getValue()<>'0' &&
                     $this->answers->id->getValue()=='0' &&
                    'AnswerDomain'.$this->question->new->getValue()==$this->answers->type->getValue()
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

    public function getSubmitButtonUsed($names=array()){
        return parent::getSubmitButtonUsed(array('next','previous','done'));
    }

    /**
     * Get the subform name based on the submit button pressed (next/previous/done)
     *
     * assumptions: subforms are in correct order
     *
     * @return boolean|string
     */
    public function getRedirectSubForm ($submitInfo){
        foreach ($this->getSubForms() as $subForm){
            $subForms[]=$subForm->getName();
        }
        $key=array_search($submitInfo['subForm'], $subForms);
        switch ($submitInfo['name']){
            case 'previous':
                if ($key>0){
                    return $subForms[$key-1];
                }else {
                    return false;
                }
                break;
            case 'next':
                if ($key<count($subForms)-1){
                    return $subForms[$key+1];
                }else {
                    return 'done';
                }
                break;
            case 'done':
                return 'done';
                break;
        }
        return false;
    }
}