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
 * Form to edit answer domain information.
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 * @author     Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_AnswerDomain_AnswerDomain extends Zend_Form
{
    /**
     * Properties form for answer domains of type text
     *
     * @return void
     * @see Zend_Form::init()
     */
    public function init()
    {
        $this->setName(get_class($this));

        $id = new Zend_Form_Element_Hidden('answerId');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $cancel->setOrder(970);
        $cancel->removeDecorator('DtDdWrapper');
        $this->addElement($cancel);

        $submitPrevious=new Zend_Form_Element_Submit('previous');
        $submitPrevious->setLabel('Previous (question)');
        $submitPrevious->setOrder(980);
        $submitPrevious->removeDecorator('DtDdWrapper');
        $this->addElement($submitPrevious);

        $submitNext=new Zend_Form_Element_Submit('next');
        $submitNext->setLabel('Next (options)');
        $submitNext->setOrder(990);
        $submitNext->removeDecorator('DtDdWrapper');
        $this->addElement($submitNext);

        $this->addDisplayGroup(
            array('cancel', 'next', 'previous', 'done'),
            'buttons',
            array('class' => 'table', 'order'=>999)
        );
    }

    /**
     * Check the answer domain properties
     *
     * @param array $values
     * @return boolean
     * @see Zend_Form::isValid()
     */
    public function isValid($values)
    {
        if ($this->isCancelled($values)) {
            return true;
        } else {
            $result = parent::isValid($values);

            return $result;
        }
    }

    /**
     * Check if the cancel button was submitted
     *
     * @param array $values
     * @return boolean
     */
    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}