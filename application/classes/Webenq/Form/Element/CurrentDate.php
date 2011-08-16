<?php
/**
 * Hidden form element that holds the current as its
 * value.
 *
 * @category	Webenq
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 *
 */
class Webenq_Form_Element_CurrentDate extends Zend_Form_Element_Hidden
{
	/**
	 * Initializes the object.
	 */
	public function init()
	{
		$this->setValue(date('Y-m-d'));
	}
}