<?php

/**
 * Webenq_Model_Category
 *
 * Manage categories
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
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
        $query->leftJoin('c.CategoryText ct');

        if ($id) {
            $query->where('c.id = ?', $id);
        } else {
            $query->orderBy('c.weight');
        }
        return $query->execute();
    }
    /**
     * Gets the category text in the given, current or preferred language. Creates
     * an empty translation if nothing was found and the category exists in the
     * database.
     *
     * @param string $language
     * @return Webenq_Model_CategoryText
     */
    public function getCategoryText($language = null)
    {
        // get current language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        // build array with available languages
        $available = array();
        foreach ($this->CategoryText as $text) {
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
        if (count($this->CategoryText) > 0)
            return $this->CategoryText[0];
        // next code obsolete? we probably need an invalid database to get here.
        // create empty translation if nothing was found
        if ($this->id) {
            $text = new Webenq_Model_CategoryText();
            $text->language = $language;
            $text->category = $this;
            $text->save();
            return $text;
        }

        return new Webenq_Model_CategoryText();
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
        if ($this->id) {
            // get translation
            $translation = Doctrine_Core::getTable('Webenq_Model_CategoryText')
                ->findOneByCategoryIdAndLanguage($this->id, $language);
            // or create new one
            if (!$translation) {
                $translation = new Webenq_Model_CategoryText();
                $translation->category_id = $this->id;
                $translation->language = $language;
            }
            // save changes
            $translation->text = $text;
            $translation->save();
        } else {
            // create new and attatch translation
            $translation = new Webenq_Model_CategoryText();
            $translation->language = $language;
            $translation->text = $text;
            $this->CategoryText[] = $translation;
        }
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
                if ($text) $this->addCategoryText($language, $text);
            }
        }
    }
}