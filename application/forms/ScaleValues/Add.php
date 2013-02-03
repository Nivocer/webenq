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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ScaleValues_Add extends Zend_Form
{
    public function init()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage("Enter a label");

        $label = $this->createElement('text', 'label');
        $label->setLabel('Label')
        ->setRequired(true)
        ->addValidator($notEmpty);

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage("Enter a value");

        $value = $this->createElement('text', 'value');
        $value->setLabel('Value')
        ->setRequired(true)
        ->addValidator($notEmpty);

        $questionType = $this->createElement('select', 'question_type');
        $questionType->setLabel('Question type')
        ->addMultiOptions(
            array(
                        'Webenq_Model_Data_Question_Closed_Scale_Two'    => '2-points scale',
                        'Webenq_Model_Data_Question_Closed_Scale_Three'    => '3-points scale',
                        'Webenq_Model_Data_Question_Closed_Scale_Four'    => '4-points scale',
                        'Webenq_Model_Data_Question_Closed_Scale_Five'    => '5-points scale',
                        'Webenq_Model_Data_Question_Closed_Scale_Six'    => '6-points scale',
                        'Webenq_Model_Data_Question_Closed_Scale_Seven'    => '7-points scale',
                )
        );

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');

        $this->addElements(array($label, $value, $questionType, $submit));
    }
}