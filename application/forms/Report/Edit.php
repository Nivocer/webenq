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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Report_Edit extends Webenq_Form_Report_Add
{
    public function init()
    {
        parent::init();
        $this->removeElement('questionnaire_id');
        $this->addElement(
            $this->createElement(
                'select', 'split_qq_id',
                array(
                    'label' => 'split question',
                    'order' => 2,
                )
            )
        );
    }
}