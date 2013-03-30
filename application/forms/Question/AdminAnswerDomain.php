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
 * Form class
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */

class Webenq_Form_Question_AdminAnswerDomain extends Zend_Form_SubForm
{
    public static $_presentationOptions;

    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
    public function init(){
        /* options form/tab */
        //numeric (open: width, slider) choice (radio/checkbox, slider, pulldown)  text (open: num rows, width)
        $this->setName(get_class($this));
        $presentationOptions=self::$_presentationOptions;
        if (empty($presentationOptions)){
            $presentationOptions=Webenq_Model_AnswerDomain::getAvailablePresentations();
        }
        $presentation=new Zend_Form_Element_Select('presentation');
        $presentation->setLabel('Presentation');
        foreach ($presentationOptions as $key => $value){
            $presentation->addMultiOption($key, $value['label']);
    }
        $this->addElement($presentation);


        //@todo dependent of presentation method, use javascript/ajax
        $presentationWidth=new Zend_Form_Element_text('presentationWidth');
        $presentationWidth->setLabel('Width of answer box');
        $presentationWidth->setDescription('todo: js-> only display if presentation is open');
        $this->addElement($presentationWidth);

        //@todo dependent of presentation method, use javascript/ajax
        //@todo only display if $presentation==open && type==text
        $presentationHeight=new Zend_Form_Element_text('presentationHeight');
        $presentationHeight->setLabel('Number of rows of answer box');
        $presentationHeight->setDescription('todo: js-> only display if presentation is open and type=text');
        $this->addElement($presentationHeight);

        $required = new Zend_Form_Element_Checkbox('required');
        $required->setLabel('Answer is required');
        $required->getDecorator('Label')->setOption('placement', 'append');
        $this->addElement($required);
        $this->addDisplayGroup(
            array('required'),
            'requiredTable',
            array('class' => 'table')
        );

        $active = new Zend_Form_Element_Checkbox('active');
        $active->setLabel('Question is active');
        $active->getDecorator('Label')->setOption('placement', 'append');

        $this->addElement($active);
        $this->addDisplayGroup(
            array('active'),
            'activeTable',
            array('class' => 'table')
        );

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $cancel->removeDecorator('DtDdWrapper');
        $this->addElement($cancel);

        $submitPrevious=new Zend_Form_Element_Submit('previous');
        $submitPrevious->setLabel('Previous (answer options)');
        $submitPrevious->removeDecorator('DtDdWrapper');
        $this->addElement($submitPrevious);

        $submitDone=new Zend_Form_Element_Submit('done');
        $submitDone->setLabel('Done');
        $submitDone->removeDecorator('DtDdWrapper');
        $this->addElement($submitDone);

        $this->addDisplayGroup(
            array('cancel', 'previous', 'next', 'done'),
            'buttons',
            array('class' => 'table', 'order'=>999)
        );
    }
}