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
 * Form to edit answer domain information within the context of editing a
 * question in a questionnaire.
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Nivocer <webenq@nivocer.com>
 */
class Webenq_Form_AnswerDomain_Sub_Form extends Zend_Form_SubForm
{
    /**
     * Add a set of checkboxes with options to a form in a display group
     *
     * @param array Array with the name for the options, the group legend
     * @param array The options to present as checkboxes, with their info
     * @return void
     */
    public function addCheckboxOptions($group, $options)
    {
        $list = array();
        foreach ($options as $item => $info) {
            $v = new Zend_Form_Element_Checkbox($item);
            $v->setBelongsTo($group['name']);
            if (isset($info['label'])) {
                $v->setLabel($info['label']);
            } else {
                $v->setLabel($item);
            }
            $v->getDecorator('Label')->setOption('placement', 'append');
            $this->addElement($v);

            $list[] = $v->getName();
        }
        if (count($list) > 0) {
            $this->addDisplayGroup(
                $list,
                $group['name'],
                array(
                    'class' => 'optionlist',
                    'legend' => $group['legend']
                )
            );
        }
    }

}