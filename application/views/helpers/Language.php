<?php
/**
 * Helper class that returns the right language from a collection
 * of doctrine objects
 */
class Zend_View_Helper_Language extends Zend_View_Helper_Abstract
{
	public function Language(Doctrine_Collection $collection, $language)
	{
		if ($collection->count() > 0) {
			foreach ($collection as $record) {
				if ($record->language == $language) {
					return $record;
				}
			}
		}
	}
}