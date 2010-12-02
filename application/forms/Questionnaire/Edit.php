<?php
class HVA_Form_Questionnaire_Edit extends HVA_Form_Questionnaire_Add
{
	/**
	 * Constructor
	 * 
	 * @param Questionnaire $questionnaire
	 * @param mixed $options
	 */
	public function __construct(Questionnaire $questionnaire, $options = null)
	{
		parent::__construct($options);
		$this->id->setValue($questionnaire->id);
	}
	
	/**
	 * Initialises the form
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->addElements(array(
			$this->createElement('hidden', 'id'),
		));
		parent::init();
	}
}