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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibility_Add extends Zend_Form
{
    /**
     * Current answer-possibility-group
     *
     * @var Webenq_Model_AnswerPossibilityGroup $_answerPossibilityGroup
     */
    protected $_answerPossibilityGroup;

    /**
     * Current language
     *
     * @var string $_language
     */
    protected $_language;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityGroup $_answerPossibilityGroup
     * @param string $language
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(
        Webenq_Model_AnswerPossibilityGroup $answerPossibilityGroup,
        $language,
        array $options = null
    )
    {
        $this->_answerPossibilityGroup = $answerPossibilityGroup;
        $this->_language = $language;

        parent::__construct($options);
    }

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'answerPossibilityGroup_id',
                    array(
                        'value' => $this->_answerPossibilityGroup->id,
                    )
                ),
                $this->createElement(
                    'select',
                    'language',
                    array(
                        'label' => 'language',
                        'multiOptions' => array(
                            'nl' => 'nl',
                        ),
                        'value' => $this->_language,
                    )
                ),
                $this->createElement(
                    'text',
                    'text',
                    array(
                        'label' => 'text',
                        'required' => true,
                    )
                ),
                $this->createElement(
                    'text',
                    'value',
                    array(
                        'label' => 'value',
                        'required' => true,
                        'validators' => array(
                            'Int',
                        ),
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