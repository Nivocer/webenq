<?php
/**
 * Import adapter for CSV documents
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Csv extends Webenq_Import_Adapter_Abstract
{
	/**
	 * Array with the data read from the file
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * Filename
	 */
	protected $_filename;

	/**
	 * File handler
	 */
	protected $_fh;

	/**
	 * Opens the file for reading
	 *
	 * @param string $filename
	 * @return self
	 */
	public function __construct($filename)
	{
		/* check if file exists */
		if (!$filename) {
			throw new Webenq_Import_Adapter_Exception("No data-file was uploaded");
		} elseif (!file_exists($filename)) {
    		throw new Webenq_Import_Adapter_Exception("Could not find data-file $filename");
    	}

    	/* open file and */
    	$this->_filename = $filename;
    	$this->_fh = fopen($filename, 'r');
	}

	/**
	 * Gets the data from the file
	 *
	 * @return array Nested array of sheets containing questions and answers
	 */
	public function getData()
	{
		if ($this->_data) return $this->_data;

		/* iterate over rows */
		$data = array();
		while ($row = fgetcsv($this->_fh)) {
			$data[0][] = $row;
		}

		$this->_data = $data;
		return $data;
	}
}