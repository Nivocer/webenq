<?php
/**
 * WebEnq4 Library
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
 * @package    WebEnq4_Forms
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Checks if a multi-lingual text array has a non-empty text in the default language
 *
 * @package    WebEnq4_Forms
 */
class WebEnq4_Validate_DateTimePicker extends Zend_Validate_Abstract
{
    const NOT_A_DATE = 'notADate';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_A_DATE => "Not a valid date",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value ...
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result = true;

        if (!is_null($value)
                && $value!== ''
                && $value!== '0000-00-00'
                && $value !== '0000-00-00 00:00:00') {

            $validator = new Zend_Validate_Date('yyyy-MM-dd');
            $result = $validator->isValid($value);
        }

        if (!$result) {
            $this->_error(self::NOT_A_DATE);
        }

        return $result;
    }
}