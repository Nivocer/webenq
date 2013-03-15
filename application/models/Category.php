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
 * Webenq_Model_Category
 *
 * Manage categories
 *
 * @package    Webenq_Models
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com
 */
class Webenq_Model_Category extends Webenq_Model_Base_Category
{

    /**
     * Get categories (all ordered by weight or one specific)
     *
     * @param string $id
     * @return Ambigous <Doctrine_Collection, mixed, PDOStatement, Doctrine_Adapter_Statement, Doctrine_Connection_Statement, unknown, number>
     */
    public static function getCategories($id = null)
    {
        $query = Doctrine_Query::create()->from('Webenq_Model_Category c');
        $query->leftJoin('c.Translation ct');

        if ($id) {
            $query->where('c.id = ?', $id);
        } else {
            $query->orderBy('c.weight');
        }
        return $query->execute();
    }
    /**
     * Gets the category text in the given, current or preferred language. Returns
     * an empty string if nothing was found.
     *
     * @param string $language
     * @return string
     */
    public function getCategoryText($language = null)
    {
        // get current language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        if (isset($this->Translation[$language])) {
            return $this->Translation[$language]->text;
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $lang) {
            if (isset($this->Translation[$lang])) {
                return $this->Translation[$lang]->text;
            }
        }

        // return any found language
        if (count($this->Translation) > 0) {
            return $this->Translation[0]->text;
        }

        // nothing, return empty string
        return '';
    }

    /**
     * Sets the category text for a given language
     *
     * @param string $language The language to set the title for
     * @param string $ext The category txt for the given language
     * @return self
     */
    public function addCategoryText($language, $text)
    {
        $this->Translation[$language]->text = $text;
        return $this;
    }

    /**
     * Sets the category texts for every language
     *
     * @param array $titles Array with language codes as keys and category texts as values
     * @return self
     */
    public function addCategoryTexts(array $texts)
    {
        foreach ($texts as $language => $text) {
            $this->addCategoryText($language, $text);
        }
    }

    public function fromArray(array $array, $deep = true)
    {
        parent::fromArray($array, $deep);

        if (isset($array['text'])) {
            foreach ($array['text'] as $language => $text) {
                if ($text) $this->Translation[$language]->text = $text;
            }
        }
    }
}