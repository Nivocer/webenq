<?php
class HVA_Form_Questionnaire_Edit extends HVA_Form_Questionnaire_Add
{
	/**
	 * Questionnaire instance
	 * 
	 * @var Questionnaire $questionnaire
	 */
	protected $_questionnaire;
	
	/**
	 * Constructor
	 * 
	 * @param Questionnaire $questionnaire
	 * @param mixed $options
	 */
	public function __construct(Questionnaire $questionnaire, $options = null)
	{
		$this->_questionnaire = $questionnaire;
		parent::__construct($options);
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
		$this->populate($this->_questionnaire->toArray());
	}
}