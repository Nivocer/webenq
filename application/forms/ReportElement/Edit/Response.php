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
class Webenq_Form_ReportElement_Edit_Response extends Webenq_Form_ReportElement_Edit
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
                'report_qq_id',
                array(
                    'label' => 'Question with uniq respondent identifier (empty when internal)',
                    'required' => true,
                    'multiOptions' => array('' => '')+ $multiOptions,
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
                'select',
                'population_qq_id',
                array(
                    'label' => 'Question with population for this group',
                    'required' => true,
                    'multiOptions' => $multiOptions,
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