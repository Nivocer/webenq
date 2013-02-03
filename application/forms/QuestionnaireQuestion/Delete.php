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
 * Form class
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_QuestionnaireQuestion_Delete extends Zend_Form
{
    /**
     * Webenq_Model_QuestionnaireQuestion instance
     *
     * @var Webenq_Model_QuestionnaireQuestion $_questionnaireQuestion
     */
    protected $_questionnaireQuestion;

    /**
     * Constructor
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @param mixed $options
     */
    public function __construct(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion, $options = null)
    {
        $this->_questionnaireQuestion = $questionnaireQuestion;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
    $qq = $this->_questionnaireQuestion;

    $this->addElements(
        array(
            $this->createElement(
                'hidden',
                'questionnaire_question_id',
                array(
                    'decorators' => array('ViewHelper'),
                    'value' => $this->_questionnaireQuestion->id,
                )
            ),
            $this->createElement(
                'submit',
                'yes',
                array(
                    'label' => 'ja',
                    'decorators' => array('ViewHelper'),
                )
            ),
            $this->createElement(
                'submit',
                'no',
                array(
                    'label' => 'nee',
                    'decorators' => array('ViewHelper'),
                )
            ),
        )
    );
    }
}