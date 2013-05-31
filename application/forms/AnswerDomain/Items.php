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
        'sortable'=>array(
            'type'=>'sortable',
            'label'=>'',
            'required'=> false
        ),
        'value' => array(
            'label' => 'Value',
            'description' => "The value stored\nin the database",
            'type' => 'string',
            'required'=> true
        ),
        'label' => array(
            'label' => 'Label',
            'description' => "How the value is presented\nin forms and reports",
            'type' => 'i18n',
            'required'=> true
        ),
        'isNullValue' => array(
            'label' => 'Null value?',
            'description' => "Should this be considered\nas \"non-response\"?",
            'type' => 'boolean',
            'required'=> false
        ),
        'isActive' => array(
            'label' => 'Active?',
            'description' => "Is this item in use?",
            'type' => 'boolean',
            'required'=> false
        ),
        'isHidden' => array(
            'label' => 'Hidden?',
            'description' => "Should this item be shown in lists?",
            'type' => 'boolean',
            'required'=> false
        ),
    );
    /**
     * Track whether item rows have been added
     */
    private $_itemsAdded = false;

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
            ->addDecorator(array('tbody'=>'HtmlTag'), array('tag' => 'tbody', 'class'=>'answeritems sortable2'))
            ->addDecorator(array('table'=>'HtmlTag'), array('tag' => 'table', 'id'=>'answeritems'));

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
        //element to store the order of the items from javascript
        $sortable=new Zend_Form_Element_Hidden('sortable');
        $sortable->removeDecorator('DtDdWrapper');
        $sortable->removeDecorator('Label');
        $this->addElement($sortable);

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
        $this->decorateAsTableRow($this->getDisplayGroup('header'),array('id'=>'headerRow'));

        // add a hidden empty row to add as new item
        $this->addItemRow('new', array('order' => 998));
        $newItemsRow = $this->getSubForm('new');
        foreach($newItemsRow->getElements() as $element){
            $element->setRequired(false);
        }

        // @todo check call of setDefaults here, maybe better to add defaults to array of fields
        $defaultItemValues=new Webenq_Model_AnswerDomainItem();
        $newItemsRow->setDefaults($defaultItemValues->toArray());
        $newItemsRow->addDecorator('HtmlTag', array(
                'tag' => 'tr',
                'class' => 'hidden',
                'id' => 'newitem'
        ));

        // button to add an item
        $cell = new WebEnq4_Form_Element_Note('addItemRow');
        $cell->setValue('<a class="add with_icon" id="addItemRow" href="#">' . t('Add an item') . '</a>');
        $this->decorateAsTableCell($cell);
        $cell->addDecorator('HtmlTag', array('tag' => 'td', 'colspan' => count($this->_fields)));
        $this->addElement($cell);
        $this->addDisplayGroup(array('addItemRow'), 'footer', array('order' => '999'));
        $this->decorateAsTableRow($this->getDisplayGroup('footer'),array('id'=>'footerRow'));

    }

    /**
     * Set defaults for items list by adding necessary rows for each item.
     *
     * If an item id is given but no sub-items are present, try to load them
     * from the database.
     *
     * @todo should move to controller
     */
    public function setDefaults(array $defaults)
    {
        // only create subforms if they are not already created
        if (!$this->_itemsAdded && isset($defaults['items'])) {
            foreach ($defaults['items'] as $key => $item) {
                if (!in_array($key, array('sortable', 'new'), true)) {
                    $this->addItemRow($key);
                }
            }
            $this->_itemsAdded = true;
        }

        parent::setDefaults($defaults);
    }

    /**
     * Add a row for a single item
     *
     * @param string Name for the row items
     * @param array Additional options to pass to the DisplayGroup
     */
    public function addItemRow($name, $options = array())
    {
        $rowForm = new WebEnq4_Form();
        $rowForm->removeDecorator('Form');

        foreach ($this->_fields as $fieldname => $fieldinfo) {
            switch ($fieldinfo['type']) {
                case 'sortable':
                    $cell= new WebEnq4_Form_Element_Note($fieldname);
                    $cell->setValue('<div class="handle" title="Drag to sort item"></div>');
                    break;
                case 'i18n':
                    $cell = new WebEnq4_Form_Element_MlText($fieldname);
                    $cell->setAttrib('languages', $this->_languages);
                    $cell->setAttrib('defaultLanguage',$this->_defaultLanguage);
                    break;
                case 'boolean':
                    $cell = new Zend_Form_Element_Checkbox($fieldname);
                    break;
                case 'string':
                default:
                    $cell = new Zend_Form_Element_Text($fieldname);
                    break;
            }
            if ($fieldinfo['required']) {
                $cell->setRequired();
            }

            if (isset($fieldinfo['description'])) {
                $cell->setAttrib('title', $fieldinfo['description']);
            }
            $rowForm->decorateAsTableCell($cell);
            $rowForm->addElement($cell);
        }

        $rowForm->setElementsBelongTo($name);

        if (isset($options['order'])) {
            $this->addSubForm($rowForm, $name, $options['order']);
        } else {
            $this->addSubForm($rowForm, $name);
        }

        //@todo set better id/don't forget to change  edit.js:addItemsrow (id).
        $this->decorateAsTableRow($this->getSubForm($name),array('id'=>$name));
    }
}