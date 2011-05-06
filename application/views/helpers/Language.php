<?php
/**
 * Helper class that returns the right language from a result set
 */
class Zend_View_Helper_Language extends Zend_View_Helper_Abstract
{
    public function language(array $collection, $language)
    {
        if (count($collection) > 0) {
            foreach ($collection as $record) {
                if (isset($record['language']) && $record['language'] == $language) {
                    return $record;
                }
            }
        }
    }
}