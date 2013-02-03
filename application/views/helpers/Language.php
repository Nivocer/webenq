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
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * View helper class
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Zend_View_Helper_Language extends Zend_View_Helper_Abstract
{
    /**
     * Return the string in the correct language from a collection of
     * records in several languages.
     *
     * @param Doctrine_Collection $collection
     * @param string $language
     */
    public function language(Doctrine_Collection $collection = null, $language)
    {
        if ($collection && $collection->count() > 0) {
            foreach ($collection as $record) {
                if ($record->language === $language) {
                    return $record;
                }
            }
        }
    }
}