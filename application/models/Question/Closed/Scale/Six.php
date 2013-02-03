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
 * Class definition for the closed question data type scale
 * @package    Webenq_Models
 */
class Webenq_Model_Question_Closed_Scale_Six extends Webenq_Model_Base_Question_Closed_Scale_Six
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array();

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        /* any values? */
        if ($question->countUnique() == 0) {
            return false;
        }

        /* more than five unique values? */
        if ($question->countUniqueExcludingNullValues() > 6) {
            return false;
        }

        /* are all values present in an answer-possibility-group? */
        $group = Webenq_Model_AnswerPossibilityGroup::findByUniqueValues($question->getUniqueValues(), $language);
        if (!$group) {
            return false;
        }

        /* does it include other values than defined for this type? */
//        if ($question->otherValuesThanDefinedValid()) {
//            return false;
//        }

        return true;
    }
}