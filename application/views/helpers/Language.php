<?php
/**
 * View helper class
 *
 * @package     Webenq
 * @subpackage  Views
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
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