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
class Webenq_Form_Question_Properties_QuestionNode extends Webenq_Form_Question_Properties
{

    public $_subFormNames=array('question', 'answer', 'options');

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
            /* question tab */
            $defaults['question'] = $defaults['QuestionnaireElement'];
        }
        parent::setDefaults($defaults);
    }
    /**
     * @return array: array with situations that needs action before redisplay form
     */
    public function getSituations()
    {
        $situations=array();
        $submitInfo=$this->_submitInfo;
        //is subform valid
        switch ($submitInfo['subForm']) {
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
