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
class Webenq_Form_AnswerDomain_Tab_Text extends Webenq_Form_AnswerDomain_Tab
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

        $name = new WebEnq4_Form_Element_MlText('name');
        $name->setAttrib('languages', $this->_languages);
        $name->setAttrib('defaultLanguage',$this->_defaultLanguage);
        $name->setBelongsTo('answers');
        $name->setLabel('Name');
        $name->setDescription('Name for later re-use of these settings');
        $name->setRequired();
        $this->addElement($name);

        $min_length = new Zend_Form_Element_Text('min_length',
            array('size' => 4, 'label' => 'Minimum length allowed'));
        $min_length->setBelongsTo('answers');
        $this->addElement($min_length);

        $max_length = new Zend_Form_Element_Text('max_length',
            array('size' => 4, 'label' => 'Maximum length allowed'));
        $max_length->setBelongsTo('answers');
        $this->addElement($max_length);

        $this->addDisplayGroup(
            array('min_length', 'max_length'),
            'minmax',
            array('class' => 'list')
        );

        $this->addValidators(Webenq_Model_AnswerDomainText::getAvailableValidators());
        $this->addFilters(Webenq_Model_AnswerDomainText::getAvailableFilters());
    }
}