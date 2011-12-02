<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit extends Zend_Form
{
    protected $_element;

    public function __construct(Webenq_Model_ReportElement $element, $options = null)
    {
        $this->_element = $element;
        parent::__construct($options);
    }
}