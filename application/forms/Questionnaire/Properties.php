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
 * Form to add or edit questionnaire properties
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 * @author     Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_Questionnaire_Properties extends Zend_Form
{
    const ERR_END_IS_BEFORE_START = 'endDateIsBeforeStartDate';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERR_END_IS_BEFORE_START => "The end date should be after the start date",
    );

    public function init()
    {
        $this->setName(get_class($this));

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $title = new WebEnq4_Form_Element_MlTextDefaultLanguage('title');
        $title->setLabel('Title');
        $title->setRequired();
        $title->setAttrib('languages', Webenq_Language::getLanguages());
        // @todo move external dependency on languages into controller/elsewhere
        $this->addElement($title);

        $category = new Zend_Form_Element_Select('category_id');
        $category->setLabel('Category');
        $categories = Webenq_Model_Category::getCategories();
        foreach ($categories as $option) {
            $category->addMultiOption($option->get('id'), $option->getCategoryText()->text);
        }
        $this->addElement($category);

        $active = new Zend_Form_Element_Checkbox('active');
        $active->setLabel('Active');
        $active->getDecorator('Label')->setOption('placement', 'append');
        $this->addElement($active);

        $date_start = new WebEnq4_Form_Element_DateTimePicker('date_start');
        $date_start->setLabel('Publish from');
        $this->addElement($date_start);

        $date_end = new WebEnq4_Form_Element_DateTimePicker('date_end');
        $date_end->setLabel('Publish until');
        $this->addElement($date_end);

        $this->addDisplayGroup(
            array('active', 'date_start', 'date_end'),
            'publishing',
            array('class' => 'table')
        );

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $cancel->removeDecorator('DtDdWrapper');
        $this->addElement($cancel);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save');
        $submit->removeDecorator('DtDdWrapper');
        $this->addElement($submit);

        $this->addDisplayGroup(
            array('cancel', 'submit'),
            'buttons',
            array('class' => 'table')
        );
    }

    public function isValid($values)
    {
        if ($this->isCancelled($values)) {
            return true;
        } else {
            $result = parent::isValid($values);

            if (isset($values["date_start"])
                    && isset($values["date_end"])
                    && ($values["date_start"]!=='')
                    && ($values["date_end"]!=='')) {
                if (strtotime($values['date_start']) > strtotime($values['date_end'])) {
                    $date_end = $this->getElement('date_end');
                    $date_end->addError($this->_messageTemplates[self::ERR_END_IS_BEFORE_START]);
                    $result = false;
                }
            }

            return $result;
        }
    }

    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}