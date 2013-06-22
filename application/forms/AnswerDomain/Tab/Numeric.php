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
 * Tab form to edit answer domain information within the context of editing a
 * question in a questionnaire.
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_AnswerDomain_Tab_Numeric extends Webenq_Form_AnswerDomain_Tab
{
    /**
     * Variable to indicate 'type' of answer domain
     */
    public $_type = 'AnswerDomainNumeric';

    /**
     * Prepare form
     *
     * @return void
     * @see Zend_Form::init()
     */
    public function init()
    {
        parent::init();

        $name = new WebEnq4_Form_Element_MlText('name');
        $name->setAttrib('languages', $this->_languages);
        $name->setAttrib('defaultLanguage',$this->_defaultLanguage);
        $name->setLabel('Name');
        $name->setDescription('Name for later re-use of these settings');
        $name->setRequired();
        $this->addElement($name);

        $min = new Zend_Form_Element_Text('min',
            array('size' => 4, 'label' => 'Minimum value allowed'));
        $this->addElement($min);

        $max = new Zend_Form_Element_Text('max',
            array('size' => 4, 'label' => 'Maximum value allowed'));
        $this->addElement($max);

        $missing = new Zend_Form_Element_Text('missing',
            array('size' => 4,
            'description' => 'Value to store if an answer is missing or declined',
            'label' => 'Value for missing answer'));
        $this->addElement($missing);

        $this->addDisplayGroup(
            array('min', 'max', 'missing'),
            'minmax',
            array('class' => 'list')
        );

        $this->addValidators(Webenq_Model_AnswerDomainNumeric::getAvailableValidators());
        $this->addFilters(Webenq_Model_AnswerDomainNumeric::getAvailableFilters());
    }
}