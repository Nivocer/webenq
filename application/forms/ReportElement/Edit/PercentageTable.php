<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_PercentageTable extends Webenq_Form_ReportElement_Edit
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

        $this->addElement($this->createElement('select', 'report_qq_id', array(
            'label' => 'reporting question',
        	'required' => true,
        	'multiOptions' => $multiOptions,
        )));

        $this->addElement($this->createElement('select', 'group_qq_id', array(
            'label' => 'grouping question',
        	'required' => false,
        	'multiOptions' => array('' => '')+ $multiOptions,
        )));

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }
}