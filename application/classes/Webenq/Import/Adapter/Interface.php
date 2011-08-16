<?php
/**
 * Import adapter interface
 *
 * Defines the minimal interface for import adapters.
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
interface Webenq_Import_Adapter_Interface
{
	/**
	 * Gets all data from the file
	 *
	 * @return array
	 */
	public function getData();

	/**
	 * Gets the value for a given cell
	 *
	 * @return string
	 */
	public function getCell();

	/**
	 * Returns the filename of the uploaded file
	 *
	 * @return string Filename
	 */
	public function getFilename();
}