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
class Webenq_Form_Management_Edit extends Zend_Form
{
    /**
     * Questionnaire question
     */
    protected $_questionnaireQuestion;

    /**
     * Class constructor
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @param array $options Zend_Form options
     * @return void
     */
    public function __construct(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion, $options = null)
    {
        $this->_questionnaireQuestion = $questionnaireQuestion;
        parent::__construct($options);
    }


    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $questionnaireQuestion = $this->_questionnaireQuestion;

        /* needed to show the default checked radio button in FireFox */
        $this->setAttrib("autocomplete", "off");

        $label = $questionnaireQuestion->Question->QuestionText[0]->text;
        if ($questionnaireQuestion->meta) {
            $meta = unserialize($questionnaireQuestion->meta);
            $valid = $meta['valid'];
            $class = $meta['class'];
        } else {
            $class = '';
        }

        $elm = new Zend_Form_Element_Radio('class');
        $elm->setLabel($label)
            ->setRequired(true)
            ->setValue(array_search($class, $valid))
            ->addMultiOptions($valid);
        $this->addElement($elm);

        $submit = new Zend_Form_Element_Submit("submit", "submit");
        $this->addElement($submit);
    }
}