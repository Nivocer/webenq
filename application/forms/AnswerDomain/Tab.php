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
 * Abstract sub form for the tab with answer domain settings and/or items.
 * Sets the default decorators to make it work as subform.
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 * @author     Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_AnswerDomain_Tab extends WebEnq4_Form
{
    /**
     * Variable to indicate 'type' of answer domain
     */
    public $_type = 'Abstract';

    /**
     * Load the default decorators, much the same as in Zend_Form_SubForm
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
                ->addDecorator('HtmlTag', array('tag' => 'dl'))
                ->addDecorator('Fieldset');
        }
        return $this;
    }

    /**
     * Adds buttons at the end of the (sub)form.
     *
     * @return void
     * @see Zend_Form::init()
     */
    public function init()
    {
        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $type = new Zend_Form_Element_Hidden('type');
        $type->removeDecorator('DtDdWrapper');
        $type->removeDecorator('Label');
        $type->setValue($this->_type);
        $this->addElement($type);

        //cancel element must not have belongsTo, action on buttons is the same
        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $this->addElement($cancel);

        $submitPrevious=new Zend_Form_Element_Submit('previous');
        $submitPrevious->setLabel('Previous');
        $this->addElement($submitPrevious);

        $submitNext=new Zend_Form_Element_Submit('next');
        $submitNext->setLabel('Next');
        $this->addElement($submitNext);

        $this->addDisplayGroup(
            array('cancel', 'previous', 'next', 'done'),
            'buttons',
            array('class' => 'table', 'order'=>999)
        );
    }

    /**
     * Adds the validators
     *
     * @param array Array of validators to add
     */
    public function addValidators($validators)
    {
        if (is_array($validators)) {
            $validatorArray = array();
            foreach ($validators as $key=>$value){
                $validatorArray[$key]=$value['label'];
            }
            if (count($validatorArray)>0) {
                $validator=new Zend_Form_Element_MultiCheckbox('validator');
                $validator->setLabel('Perform these validations before accepting an answer');
                $validator->setMultiOptions($validatorArray);
                $validator->setAttrib('class', 'optionlist');
                $this->addElement($validator);
            }
        }
    }

    /**
     * Adds the filters
     *
     * @param array Array of filters to add
     */
    public function addFilters($filters)
    {
        if (is_array($filters)) {
            $filterArray = array();
            foreach ($filters as $key=>$value){
                $filterArray[$key]=$value['label'];
            }
            if (count($filterArray)>0) {
                $filter=new Zend_Form_Element_MultiCheckbox('filter');
                $filter->setLabel('Apply these changes before storing an answer:');
                $filter->setAttrib('class', 'optionlist');
                $filter->setMultiOptions($filterArray);
                $this->addElement($filter);
            }
        }
    }
}