<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportElement_Add extends Zend_Form
{
    protected $_types = array(
        'text' => 'text',
    	'text with info' => 'text with info',
    	'open question' => 'open question',
    	'percentages table' => 'percentages table',
    	'mean table' => 'mean table',
    	'barchart and mean' => 'barchart and mean',
    	'response'=> 'response',
    	'include jasper subreport'=>'include jasper subreport',
    );

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElement($this->createElement('select', 'type', array(
            'label' => 'element type',
        	'required' => true,
            'multiOptions' => $this->_types,
        )));


        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }
}