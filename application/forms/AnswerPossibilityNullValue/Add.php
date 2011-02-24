<?php
class Webenq_Form_AnswerPossibilityNullValue_Add extends Zend_Form
{
	/**
	 * Builds the form
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->addElements(array(
			$this->createElement('text', 'value', array(
				'label' => 'Naam:',
				'required' => true,
				'validators' => array(
					new Zend_Validate_NotEmpty(),
				),
				'filters' => array(
					new Zend_Filter_StringToLower(),
				),
			)),
			$this->createElement('submit', 'submit', array(
				'label' => 'opslaan',
			)),
		));
	}
}