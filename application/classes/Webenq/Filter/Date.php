<?php
/**
 * Class for filtering dates
 *
 * Converts a date from dd-mm-yyyy to yyyy-mm-dd
 * or vice versa, depending on the provided value.
 *
 * @package		Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Filter_Date implements Zend_Filter_Interface
{
    /**
     * Filters the given date
     *
     * Overrides the filter method defined by
     * Zend_Filter_Interface. Returns the converted
     * date (dd-mm-yyyy to yyyy-mm-dd or vice versa,
     * depending on the given value).
     *
     * @param  string $value
     * @return string
     * @see Zend_Filter_Interface
     */
    public function filter($value)
    {
    	if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $value, $m)) {
    		return $m[3] . '-' . $m[2] . '-' . $m[1];
    	}
    }
}
