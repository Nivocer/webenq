<?php
/**
 * Interface that must be implemented by import classes
 * for importing data into Webenq.
 *
 * @package     Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
interface Webenq_Import_Interface
{
    /**
     * Class constructor
     *
     * @param Webenq_Import_Adapter_Abstract $adapter
     * @param string $language
     */
    public function __construct(Webenq_Import_Adapter_Abstract $adapter, $language);

	/**
	 * The actual method for handling the import.
	 */
	public function import();
}