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
     * Prepare form
     *
     * @return void
     * @see Zend_Form::init()
     */
    public function init()
    {
        parent::init();

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name');
        $name->setDescription('For later re-use of these settings');
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

        $this->addCheckboxOptions(
                array(
                        'name' => 'validator',
                        'legend' => 'Perform these validations before accepting an answer:'
                ),
                Webenq_Model_AnswerDomainNumeric::getAvailableValidators()
        );

        $this->addCheckboxOptions(
            array(
                'name' => 'filter',
                'legend' => 'Apply these changes before storing an answer:'
            ),
            Webenq_Model_AnswerDomainNumeric::getAvailableFilters()
        );
    }
}