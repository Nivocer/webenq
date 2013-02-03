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
 * AnswerPossibility class definition
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_AnswerPossibility extends Webenq_Model_Base_AnswerPossibility
{
    /**
     * Gets the answer possibility text in the given, current or preferred language
     *
     * @param string $language
     * @return Webenq_Model_AnswerPossibilityText
     * @throws Exception
     */
    public function getAnswerPossibilityText($language = null)
    {
        // get curren language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        // build array with available languages
        $available = array();
        foreach ($this->AnswerPossibilityText as $text) {
            $available[$text->language] = $text;
        }

        // return current language if set
        if (key_exists($language, $available)) {
            return $available[$language];
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $preferredLanguage) {
            if (key_exists($preferredLanguage, $available)) {
                return $available[$preferredLanguage];
            }
        }

        // return any found language
        return $this->AnswerPossibilityText[0];

        // throw Exception if no translation was found
        throw new Exception(
            'No translation was found for ' . get_class($this) .
            ' with ID ' . $this->id
        );
    }

    /**
     * Builds an array with available languages as values
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        $available = array();
        foreach ($this->AnswerPossibilityText as $text) {
            $available[] = $text->language;
        }
        return $available;
    }
}
