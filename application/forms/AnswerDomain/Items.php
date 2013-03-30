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
 * Subform to edit answer domain information within the context of editing a
 * question in a questionnaire.
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_AnswerDomain_Items extends WebEnq4_Form
{
    /**
     * List of fields to show for items
     */
    private $_fields = array(
        'value' => array(
            'label' => 'Value',
            'description' => 'Stored in the database',
            'type' => 'string'
        ),
        'label' => array(
            'label' => 'Label',
            'description' => 'Presented in forms and reports',
            'type' => 'i18n'
        ),
        'isNullValue' => array(
            'label' => 'Null value?',
            'description' => 'Consider this as "non-response"?',
            'type' => 'boolean'
        ),
        'isActive' => array(
            'label' => 'Active?',
            'description' => 'Is this item in use?',
            'type' => 'boolean'
        ),
        'isHidden' => array(
            'label' => 'Hidden?',
            'description' => 'Should this item be shown in lists?',
            'type' => 'boolean'
        ),
    );

    /**
     * Load the default decorators
     *
     * @return Webenq_Form_AnswerDomain_Items
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'table'));
        }
        return $this;
    }

    /**
     * Subform to ask answer domain properties when editing a question
     *
     * @return void
     * @see Zend_Form::init()
     */
    public function init()
    {
        $id = new Zend_Form_Element_Hidden('id');
        $id->setBelongsTo($this->getName());
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $this->addElement($id);

        // add the table headers
        $header = array();
        foreach ($this->_fields as $fieldname => $fieldinfo) {
            $cell = new WebEnq4_Form_Element_Note('th_'.$fieldname);
            $cell->setValue($fieldinfo['label']);
            $this->decorateAsTableCell($cell, true);
            $this->addElement($cell);
            $header[] = $cell->getName();
        }
        $this->addDisplayGroup($header, 'header', array());
        $this->decorateAsTableRow($this->getDisplayGroup('header'));

        // add an empty row to add a new item
        //$this->addItemRow('new_item', array('order'=>'999'));
    }

    /**
     * Add a row for a single item
     *
     * @param string Name for the row items
     * @param array Additional options to pass to the DisplayGroup
     */
    public function addItemRow($name, $options = array())
    {
        $row = array();
        foreach ($this->_fields as $fieldname => $fieldinfo) {
            switch ($fieldinfo['type']) {
                case 'i18n':
                    // @todo this doesn't work yet... we're just picking the English translation
                    $cell = new Zend_Form_Element_Text($fieldname);
                    $cell->setBelongsTo($name . '[Translation][en]');
                    break;
                case 'boolean':
                    $cell = new Zend_Form_Element_Checkbox($fieldname);
                    $cell->setBelongsTo($name);
                    break;
                case 'string':
                default:
                    $cell = new Zend_Form_Element_Text($fieldname);
                    $cell->setBelongsTo($name);
                    break;
            }

            $this->addElement($cell);
            $this->decorateAsTableCell($cell);
            $row[] = $cell->getName();
        }
        $this->addDisplayGroup($row, $name, $options);
        $this->decorateAsTableRow($this->getDisplayGroup($name));
    }
}