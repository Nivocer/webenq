<?php
/**
 * Import adapter for XLS documents
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Xls extends Webenq_Import_Adapter_Abstract
{
	/**
	 * Reader class
	 */
	protected $_reader = 'Excel5';

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
	 * Array with working sheets from data file
	 */
	protected $_sheets = array();

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
			throw new Exception("No data-file was uploaded");
		} elseif (!file_exists($filename)) {
    		throw new Exception("Could not find data-file $filename");
    	}

    	$this->_filename = $filename;

    	/* load library */
    	require_once 'PHPExcel/PHPExcel.php';

    	/* open file and read first sheet */
		$reader = PHPExcel_IOFactory::createReader($this->_reader);
		$reader->setReadDataOnly(true);
		$document = $reader->load($filename);
		$this->_sheets = $document->getAllSheets();
	}

	/**
	 * Gets the data from the file
	 *
	 * @return array Nested array of sheets containing questions and answers
	 */
	public function getData()
	{
		if ($this->_data) return $this->_data;

		/* iterate over sheets */
		$data = array();
		foreach ($this->_sheets as $sheet) {
			$data[] = array_merge($sheet->toArray(), array());
		}

		// convert date fields
		foreach ($data as $sheetId => $sheet) {
		    foreach ($sheet as $rowId => $row) {
                if ($rowId > 0) {
    		        foreach ($row as $cellId => $cell) {
    		            if (is_float($cell) && $cell >= PHPExcel_Shared_Date::PHPToExcel(0)) {
    		                $data[$sheetId][$rowId][$cellId] = gmdate('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($data[$sheetId][$rowId][$cellId]));
    		            }
    		        }
                }
		    }
		}

		$this->_data = $data;
		return $data;
	}
}