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
class Webenq_Form_AnswerPossibilityGroup_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-group
     *
     * @var Webenq_Model_AnswerPossibilityGroup $_answerPossibilityGroup
     */
    protected $_answerPossibilityGroup;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityGroup $answerPossibilityGroup
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibilityGroup $answerPossibilityGroup, array $options = null)
    {
        $this->_answerPossibilityGroup = $answerPossibilityGroup;
        parent::__construct($options);
    }

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->setAttrib('autocomplete', 'off');

        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'id',
                    array(
                        'value' => $this->_answerPossibilityGroup->id,
                    )
                ),
                $this->createElement(
                    'text',
                    'name',
                    array(
                        'label' => 'name',
                        'value' => $this->_answerPossibilityGroup->name,
                    )
                ),
                $this->createElement(
                    'text',
                    'number',
                    array(
                        'label' => 'number of allowed answers',
                        'value' => $this->_answerPossibilityGroup->number,
                        'validators' => array('Int'),
                    )
                ),
                $this->createElement(
                    'radio',
                    'measurement_level',
                    array(
                    'label' => 'measurement level',
                    'multiOptions' => array(
                        'metric' => 'metric',
                        'non-metric' => 'non-metric',
                    ),
                    'value' => $this->_answerPossibilityGroup->measurement_level,
                    'required' => true,
                    'validators' => array('NotEmpty'),
                    )
                ),
                $this->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'save',
                    )
                ),
            )
        );
    }
}