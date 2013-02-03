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
 * Class definition for closed question data types
 * @package    Webenq_Models
 */
class Webenq_Model_Question_Closed extends Webenq_Model_Base_Question_Closed
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array('Scale', 'Percentage');

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     * @todo make this numbers configurable
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        /* any values? */
        if ($question->countUnique() == 0) {
            return false;
        }

        /* not too many different answers? */
        if ($question->countUnique() > 50) {
            return false;
        }

        /* not too many different answers (absolute and relative to number of answers)? */
        if ($question->countUnique() > 7 && $question->countUnique() / $question->count() > .333) {
            return false;
        }

        /* not too much difference in length of answers? */
        if ($question->diffLen() > 100) {
            return false;
        }

        return true;
    }
}