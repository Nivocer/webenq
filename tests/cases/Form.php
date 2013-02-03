<?php
/**
 * Webenq
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
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
abstract class Webenq_Test_Case_Form extends Webenq_Test_Case_Fixture
{
    protected $_form;

    public function getForm()
    {
        if (!is_object($this->_form)) {
            $formClass = get_class($this);
            $formClass = str_replace('Test_', null, $formClass);
            $formClass = str_replace('Test', null, $formClass);
            $this->_form = new $formClass;
        }

        $this->_form->reset();
        return $this->_form;
    }

    /**
     * Scans all subforms and elements for errors
     *
     * @param Zend_Form|Zend_Form_Element $form
     * @return boolean
     */
    public function hasErrors($form)
    {
        if ($form instanceof Zend_Form_Element) {
            return $this->_elementHasErrors($form);
        } elseif ($form instanceof Zend_Form) {
            return $this->_formHasErrors($form);
        }
    }

    protected function _formHasErrors(Zend_Form $form)
    {
        foreach ($form->getSubForms() as $subForms) {
            if ($this->_formHasErrors($subForms)) return true;
        }

        foreach ($form->getElements() as $element) {
            if ($this->_elementHasErrors($element)) return true;
        }

        return false;
    }

    protected function _elementHasErrors(Zend_Form_Element $element)
    {
        return (count($element->getErrors()) > 0);
    }
}