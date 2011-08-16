<?php
/**
 * Class for language and translation matters within Webenq
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