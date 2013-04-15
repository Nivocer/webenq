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
     * Variable to indicate 'type' of answer domain
     */
    public $_type = 'AnswerDomainChoice';

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
        $name = new WebEnq4_Form_Element_MlText('name');
        $name->setAttrib('languages', $this->_languages);
        $name->setAttrib('defaultLanguage',$this->_defaultLanguage);
        $name->setBelongsTo('answers');
        $name->setLabel('Name');
        $name->setDescription('Name for this set of choices');
        $name->setRequired();
        $this->addElement($name);

        $itemId = new Zend_Form_Element_Hidden('answer_domain_item_id');
        $itemId->setBelongsTo('answers');
        $itemId->removeDecorator('DtDdWrapper');
        $itemId->removeDecorator('Label');
        $this->addElement($itemId);


        // keep the items subform to be able to add things to it in setDefaults
        $this->items = new Webenq_Form_AnswerDomain_Items();
        $this->items->_languages = $this->_languages;
        $this->addSubForm($this->items, 'items');

        $this->addValidators(Webenq_Model_AnswerDomainChoice::getAvailableValidators());
        $this->addFilters(Webenq_Model_AnswerDomainChoice::getAvailableFilters());
    }

    /**
     * Set defaults for all elements
     */
    public function setDefaults(array $defaults)
    {
        $defaults['items']['source']='';
        if (isset($defaults['AnswerDomainItem'])) {
            $defaults['items'] = $defaults['AnswerDomainItem'];
            //@todo saver method to determin source of input
            $defaults['items']['source'] ='model';
        } else {
            $defaults['items']['source']='form';
        }
        parent::setDefaults($defaults);
    }
}