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
 * @package    Webenq_Reports_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Reports_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_MeanTable extends Webenq_Form_ReportElement_Edit
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $multiOptions = array();
        foreach ($this->_element->Report->Questionnaire->QuestionnaireQuestion as $qq) {
            $multiOptions[$qq->id] = $qq->Question->getQuestionText()->text;
        }

        $this->addElement(
            $this->createElement(
                'select',
                'header_qq_id',
                array(
                    'label' => 'header question',
                    'required' => true,
                    'multiOptions' => $multiOptions,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'multiCheckbox',
                'report_qq_ids',
                array(
                    'label' => 'reporting questions',
                    'required' => true,
                    'multiOptions' => $multiOptions,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'group_qq_id',
                array(
                    'label' => 'grouping question',
                    'required' => false,
                    'multiOptions' => array('' => '')+ $multiOptions,
                )
            )
        );
        $this->addElement(
            $this->createElement(
                'radio',
                'display_group_question_text',
                array(
                    'label' => 'Display the group question text on top of the table',
                    'required' => true,
                    'multiOptions' => array(
                        'no' => 'no, don\'t display group question text',
                        'yes'=>'yes, display group question text'
                    ),
                )
            )
        );
        $this->addElement(
            $this->createElement(
                'select',
                'color_mean',
                array(
                    'label' => 'color the means',
                    'required' => true,
                    'multiOptions' => array(
                        'no' => 'no color',
                        'yes' => 'colored by mean'
                    ),
                )
            )
        );
        $this->addElement(
            $this->createElement(
                'radio',
                'variant',
                array(
                    'label' => 'Variant of the table',
                    'required' => true,
                    'multiOptions' => array(
                        'questionsInRows'=>'questionsInRows',
                        'questionsInColumns'=>'questionsInColumns',
                        'lowScores'=>'lowScores',
                        'lowScoresDepartmentC'=>'lowScoresDepartmentC',
                        'grade'=>'grade'
                    ),
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'save',
                )
            )
        );
    }
}
