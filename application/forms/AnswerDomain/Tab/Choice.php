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
class Webenq_Form_AnswerDomain_Tab_Choice extends Webenq_Form_AnswerDomain_Tab
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

        // @todo hard-coded to English version of the name now
        $name = new Zend_Form_Element_Text('en');
        $name->setBelongsTo('answers[name]');
        $name->setLabel('Name');
        $name->setDescription('Name of this set of choices');
        $name->setRequired();
        $this->addElement($name);

        // keep the items subform to be able to add things to it in setDefaults
        $this->items = new Webenq_Form_AnswerDomain_Items();
        $this->items->_languages = $this->_languages;
        $this->addSubForm($this->items, 'items');

        $this->addCheckboxOptions(
            array(
                'name' => 'validator',
                'legend' => 'Perform these validations before accepting an answer:'
            ),
            Webenq_Model_AnswerDomainChoice::getAvailableValidators()
        );

        $this->addCheckboxOptions(
            array(
                'name' => 'filter',
                'legend' => 'Apply these changes before storing an answer:'
            ),
            Webenq_Model_AnswerDomainChoice::getAvailableFilters()
        );
    }

    /**
     * Set defaults for all elements
     */
    public function setDefaults(array $defaults)
    {
        if (isset($defaults['AnswerDomainItem'])) {
            $defaults['items'] = $defaults['AnswerDomainItem'];
        }

        parent::setDefaults($defaults);
    }
}