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
class Webenq_Form_QuestionnaireQuestion_Add extends Zend_Form
{
    /**
     * Id of the current questionnaire
     *
     * @var int $_questionnaireId
     */
    protected $_questionnaireId;

    /**
     * Constructor
     *
     * @param int $questionnaireId Questionnaire to which the question must be added
     * @param mixed $options
     */
    public function __construct($questionnaireId, $options = null)
    {
        $this->_questionnaireId = $questionnaireId;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
    $this->addElements(
        array(
            $this->createElement(
                'hidden',
                'id',
                array(
                    'required' => true,
                    'decorators' => array('ViewHelper'),
                )
            ),
            $this->createElement(
                'hidden',
                'questionnaire_id',
                array(
                    'required' => true,
                    'value' => $this->_questionnaireId,
                    'decorators' => array('ViewHelper'),
                )
            ),
            $this->createElement(
                'text',
                'filter',
                array(
                    'label' => 'Filter:',
                    'autocomplete' => 'off',
                )
            ),
        )
    );
    }
}