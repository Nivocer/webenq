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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Test_Index extends Zend_Form
{
    /**
     * Builds the form
     */
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $notEmpty = new Zend_Validate_NotEmpty();
        $count = new Zend_Validate_File_Count(
            array(
                'min' => 1,
                'max' => 1)
        );

        $file = $this->createElement('file', 'file');
        $file
        ->setRequired(true)
        ->setLabel('Select a file containing testdata:')
        ->addValidators(
            array(
               $notEmpty,
               $count,
                )
        );

        $submit = $this->createElement('submit', 'Test');

        $this->addElements(array($file, $submit));
    }
}