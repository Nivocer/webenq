<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_MeanTable extends Webenq_Form_ReportElement_Edit
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

        $this->addElement($this->createElement('select', 'header_qq_id', array(
            'label' => 'header question',
        	'required' => true,
        	'multiOptions' => $multiOptions,
        )));

        $this->addElement($this->createElement('multiCheckbox', 'report_qq_ids', array(
            'label' => 'reporting questions',
        	'required' => true,
        	'multiOptions' => $multiOptions,
        )));

        $this->addElement($this->createElement('select', 'group_qq_id', array(
            'label' => 'grouping question',
        	'required' => false,
        	'multiOptions' => array('' => '')+ $multiOptions,
        )));

        $this->addElement($this->createElement('select', 'color_schema', array(
            'label' => 'color schema',
        	'required' => true,
        	'multiOptions' => array('white' => 'white', 'mean5' => 'mean5', 'mean10' => 'mean10'),
        )));

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }
}