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
class Webenq_Form_QuestionnaireNode_Properties_LikertNode extends Webenq_Form_QuestionnaireNode_Properties_GroupNode
{
    public $_subFormNames = array('group', 'questions', 'answer', 'likertOptions');

    /**
     * Set defaults for likertNode properties form
     *
     * @param array Array with data for a questionnaire node
     */
    public function setDefaults(array $defaults)
    {

    /* options tab */
        //get defaults from answerDomain
        if (isset($defaults['QuestionnaireElement'])) {
            if (isset($defaults['QuestionnaireElement']['AnswerDomain'])) {
                $defaults['likertOptions']=$defaults['QuestionnaireElement']['AnswerDomain'];
            }
            //override from options
            if (isset($defaults['QuestionnaireElement']['options']['options'])){
                foreach ($defaults['QuestionnaireElement']['options']['options'] as $key=> $value){
                    $defaults['likertOptions'][$key]=$value;
                }
            }
            //override from questionnaireElement
            if (isset($defaults['QuestionnaireElement']['active'])) {
                $defaults['likertOptions']['active'] = $defaults['QuestionnaireElement']['active'];
            }
        }
        parent::setDefaults($defaults);
    }
}
