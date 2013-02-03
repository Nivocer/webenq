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
 * @package    Webenq_Reports_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Reports_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_Text extends Webenq_Form_ReportElement_Edit
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElement(
            $this->createElement(
                'textarea',
                'text', array(
                    'label' => 'text',
                    'required' => true,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'multiCheckbox',
                'report_for_subgroup',
                array(
                    'label' => 'Display text if we create reports for subgroups (split)',
                    'required' => false,
                    'multiOptions' => array(
                        'onlyForSubGroups'=>'Only for subgroups'
                    ),
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'save',
                )
            )
        );
    }
}