<?php
class Webenq_Form_Questionnaire_Edit extends Webenq_Form_Questionnaire_Add
{
	/**
	 * Questionnaire instance
	 * 
	 * @var array $questionnaire
	 */
	protected $_questionnaire;
	
	/**
	 * Constructor
	 * 
	 * @param array $questionnaire
	 * @param mixed $options
	 */
	public function __construct(array $questionnaire, $options = null)
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
		$this->setName(get_class($this));
		$this->addElements(array(
			$this->createElement('hidden', 'id'),
		));
		parent::init();
		$this->populate($this->_questionnaire);
	}
}