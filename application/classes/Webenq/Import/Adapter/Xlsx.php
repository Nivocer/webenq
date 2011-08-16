<?php
/**
 * Import adapter for XLSX documents
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Xlsx extends Webenq_Import_Adapter_Xls
{
	/**
	 * Reader class
	 *
	 * @var string
	 */
	protected $_reader = 'Excel2007';
}