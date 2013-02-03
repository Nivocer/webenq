<?php
/**
 * Webenq
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
 * Class for filtering dates
 *
 * Converts a date from dd-mm-yyyy to yyyy-mm-dd
 * or vice versa, depending on the provided value.
 *
 * @package		Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Filter_Date implements Zend_Filter_Interface
{
    /**
     * Filters the given date
     *
     * Overrides the filter method defined by
     * Zend_Filter_Interface. Returns the converted
     * date (dd-mm-yyyy to yyyy-mm-dd or vice versa,
     * depending on the given value).
     *
     * @param  string $value
     * @return string
     * @see Zend_Filter_Interface
     */
    public function filter($value)
    {
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $value, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }
    }
}
