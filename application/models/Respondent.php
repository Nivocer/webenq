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
 * @package    Webenq_Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Respondent
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_Respondent extends Webenq_Model_Base_Respondent
{
    public function getAnswer(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
        $answers = Doctrine_Query::create()
            ->from('Webenq_Model_Answer a')
            ->where('a.respondent_id = ?', $this->id)
            ->andWhere('a.questionnaire_question_id = ?', $questionnaireQuestion->id)
            ->limit(1)
            ->execute();

        if (count($answers) === 1) {
            return $answers->getFirst();
        }

        return false;
    }
}