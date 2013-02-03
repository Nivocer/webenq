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
 * @package    Webenq_Questionnaires_Collect
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Questionnaires_Collect
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Questionnaire_Collect extends Zend_Form
{
    /**
     * Array with questions
     *
     * @var Doctrine_Collection
     */
    protected $_questions;

    /**
     * Respondent
     *
     * @var Webenq_Model_Respondent
     */
    protected $_respondent;

    /**
     * Class constructor
     *
     * @param Doctrine_Collection $questions
     * @param Webenq_Model_Respondent $respondent (optional)
     *      If provided the form elements are filled with the respondent's answers
     * @param array $options
     */
    public function __construct(Doctrine_Collection $questions,
        Webenq_Model_Respondent $respondent, $options = null)
    {
        $this->_questions = $questions;
        $this->_respondent = $respondent;
        parent::__construct($options);
    }

    public function init()
    {
        // add questions
        foreach ($this->_questions as $question) {

            $name = "qq_$question->id";
            $elm = $question->getFormElement($this->_respondent);

            if ($elm instanceof Zend_Form_Element) {
                $this->addElement($elm, $name);
            } elseif ($elm instanceof Zend_Form_SubForm) {
                $this->addSubform($elm, $name);
            }
        }

        // add submit button
        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'continue',
                )
            )
        );
    }

    /**
     * Retrieve a single element
     *
     * @param  string $name
     * @return Zend_Form_Element|null
     * @todo make recursive
     */
    public function getElement($name)
    {
        $element = parent::getElement($name);
        if ($element) {
            return $element;
        } else {
            foreach ($this->getSubForms() as $subForm) {
                $element = $subForm->getElement($name);
                if ($element) {
                    return $element;
                } else {
                    $subSubForms = $subForm->getSubForms();
                    foreach ($subSubForms as $subSubForm) {
                        $element = $subSubForm->getElement($name);
                        if ($element) {
                            return $element;
                        }
                    }
                }
            }
        }
        return null;
    }
}