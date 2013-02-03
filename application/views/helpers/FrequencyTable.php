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
 * Helper class for rendering a frequency table
 * @package    Webenq
 */
class Zend_View_Helper_FrequencyTable extends Zend_View_Helper_Abstract
{
    public function frequencyTable(Webenq_Model_QuestionnaireQuestion $qq)
    {
        $frequency = array();
        foreach ($qq['Answer'] as $answer) {
            if (key_exists($answer['answerPossibility_id'], $frequency)) {
                $frequency[$answer['answerPossibility_id']]++;
            } else {
                $frequency[$answer['answerPossibility_id']] = 1;
            }
        }

        $html = '
            <table>
                <tbody>
                    <tr>
                        <th>' . t('id') . '</th>
                        <th>' . t('label') . '</th>
                        <th>' . t('value') . '</th>
                        <th>' . t('count') . '</th>
                    </tr>';

        $hasRows = false;
        foreach ($frequency as $id => $count) {

            $answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')->find($id);

            if ($answerPossibility) {
                $hasRows = true;
                $html .= '
                        <tr>
                            <td>' . $answerPossibility->id . '</td>
                            <td>' . $answerPossibility->AnswerPossibilityText[0]->text . '</td>
                            <td>' . $answerPossibility->value . '</td>
                            <td>' . $count . '</td>
                        </tr>';
            }
        }

        $html .= '
                </tbody>
            </table>';

        if ($hasRows) return $html;
    }
}