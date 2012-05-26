<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_TextWithInfo extends Webenq_Form_ReportElement_Edit

{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
    	$multiOptions = array();
    	foreach ($this->_element->Report->Questionnaire->QuestionnaireQuestion as $qq) {
    		$multiOptions[$qq->id] = $qq->Question->getQuestionText()->text;
    	}
    	$this->addElement($this->createElement('textarea', 'text', array(
            'label' => 'text use [count] in text to show number of valid answer in this report',
        	'required' => true,
        )));
        

        $this->addElement($this->createElement('select', 'report_qq_id', array(
        	'label' => 'reporting question',
        	'required' => true,
        	'multiOptions' => $multiOptions,
        )));

                
        
        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }
}