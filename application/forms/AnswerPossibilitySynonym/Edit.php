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
class Webenq_Form_AnswerPossibilitySynonym_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-text-synonym
     *
     * @var Webenq_Model_AnswerPossibilityTextSynonym $_synonym
     */
    protected $_synonym;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityTextSynonym $_synonym
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibilityTextSynonym $synonym, array $options = null)
    {
        $this->_synonym = $synonym;
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
                    'id',
                    array(
                        'value' => $this->_synonym->id,
                    )
                ),
                $this->createElement(
                    'text',
                    'text',
                    array(
                        'label' => 'text',
                        'value' => $this->_synonym->text,
                        'required' => true,
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