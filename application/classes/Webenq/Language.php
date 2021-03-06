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
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Class for language and translation matters within Webenq
 * @package    Webenq_Application
 */
class Webenq_Language
{
    protected static $_defaultLanguages = array(
            'nl' => 'nl',
            'en' => 'en',
    );

    /**
     * Returns the languages currently supported by the application
     *
     * @return array
    */
    static public function getLanguages()
    {
        $languages = Doctrine_Query::create()
        ->select('qt.language')
        ->from('Webenq_Model_QuestionText qt')
        ->groupBy('qt.language')
        ->orderBy('qt.language')
        ->execute();

        $foundLanguages = array();
        foreach ($languages as $language) {
            $foundLanguages[$language->language] = $language->language;
        }

        return array_merge($foundLanguages, self::$_defaultLanguages);
    }
}