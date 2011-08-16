<?php
/**
 * Abstract class containing methods that are shared
 * by all importer classes. All importers must inherit
 * this class.
 *
 * @package		Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
abstract class Webenq_Import_Abstract implements Webenq_Import_Interface
{
	/**
	 * Supported types
	 *
	 * @var array
	 */
	public static $supportedTypes = array(
		'default' => 'default',
		'questback' => 'questback',
	);

	/**
	 * Adapter instance
	 *
	 * @var Webenq_Import_Adapter_Abstract $_adapter
	 */
	protected $_adapter;

    /**
     * Import language
     *
     * @var string $_language
     */
    protected $_language;

	/**
	 * Constructor
	 *
	 * @param Webenq_Import_Adapter_Abstract $adapter
	 * @param string $language
	 */
	public function __construct(Webenq_Import_Adapter_Abstract $adapter, $language)
	{
		$this->_adapter = $adapter;
        $this->_language = $language;
	}

	/**
	 * Factors an instance of Webenq_Import_Abstract, depending on the
	 * given type.
	 *
	 * @param string $type
	 * @param Webenq_Import_Adapter_Abstract $adapter
	 * @param string $language
	 * @return Webenq_Import_Abstract
	 */
	public static function factory($type, Webenq_Import_Adapter_Abstract $adapter, $language)
	{
	    if (in_array($type, self::$supportedTypes)) {
        	$class = 'Webenq_Import_' . ucfirst($type);
        	return new $class($adapter, $language);
	    }

	    throw new Exception('Unknown type given in Webenq_Import_Abstract::factory()');
	}

	/**
	 * Converts the data retrieved from the adapter to an array with questions
	 * as keys and answers as array with values.
	 *
	 * @param array $data Data retrieved from the adapter
	 * @return array Questions as keys and answers as array with values
	 */
	public function _getDataAsAnswers($data)
	{
		$return = array();
		$questions = array_shift($data);

		foreach ($questions as $col => $question) {
			foreach ($data as $row) {
				$return[$question][] = $row[$col];
			}
		}

		return $return;
	}
}