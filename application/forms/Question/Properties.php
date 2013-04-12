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
    public $_defaultLanguage;

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
        parent::__construct();
    }

    public function init()
    {
        /* question text and type tab */
        $question = new Webenq_Form_Question_Tab_Question(array('defaultLanguage'=>$this->_defaultLanguage));
        $question->removeDecorator('DtDdWrapper');

        $node_id = new Zend_Form_Element_Hidden('node_id');
        $node_id->removeDecorator('DtDdWrapper');
        $node_id->removeDecorator('Label');
        $question->addElement($node_id);

        $this->addSubForm($question, 'question');

        /* determine appropriate tab forms for answer settings and options */
        $answerDomainType = $this->answerDomainType;

        if (in_array($answerDomainType, array('AnswerDomainChoice', 'AnswerDomainNumeric', 'AnswerDomainText'))) {
            $classAnswers = 'Webenq_Form_AnswerDomain_Tab_' . substr($answerDomainType, 12);
            $classOptions = 'Webenq_Form_Question_Tab_' . substr($answerDomainType, 12);
        } else {
            $classAnswers = 'Webenq_Form_AnswerDomain_Tab';
            $classOptions = 'Webenq_Form_Question_Tab';
        }

        /* answer domain settings tab */
        $answers = new $classAnswers(array('defaultLanguage'=>$this->_defaultLanguage));
        $answers->removeDecorator('DtDdWrapper');
        $this->addSubForm($answers, 'answers');

        /* question options settings tab */
        $options = new $classOptions(array('defaultLanguage'=>$this->_defaultLanguage));
        $options->removeDecorator('DtDdWrapper');
        $this->addSubForm($options, 'options');
    }

    /**
     * Set defaults for all elements
     */
    public function setDefaults(array $defaults)
    {
        //question tab
        if (isset($defaults['QuestionnaireElement'])) {
            $defaults['question'] = $defaults['QuestionnaireElement'];
            $defaults['question']['node_id'] = $defaults['id'];

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

    public function getSituations()
    {
        return array('X');
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