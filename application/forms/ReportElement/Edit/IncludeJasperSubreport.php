<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_IncludeJasperSubreport extends Webenq_Form_ReportElement_Edit
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElement($this->createElement('text', 'filename', array(
            'label' => t('jasper file name (relative to java-root'),
        	'required' => true,
        )));
        
        
        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }
}