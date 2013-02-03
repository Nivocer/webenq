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
 * Class definition for the open question data type number
 * @package    Webenq_Models
 */
class Webenq_Model_Question_Open_Number extends Webenq_Model_Base_Question_Open_Number
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
        /* are values numeric only? */
        if (!$question->isNumeric()) {
            return false;
        }

        return true;
    }


    /**
     * Returns the sum of all values
     *
     * @return int|float Sum of values
     */
    public function sum()
    {
        return array_sum($this->getAnswerValues());
    }
}