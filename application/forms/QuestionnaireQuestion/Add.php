<?php
class HVA_Form_QuestionnaireQuestion_Add extends Zend_Form
{
	/**
	 * Id of the current questionnaire
	 * 
	 * @var int $_questionnaireId
	 */
	protected $_questionnaireId;
	
	/**
	 * Constructor
	 * 
	 * @param int $questionnaireId
	 * @param mixed $options
	 */
	public function __construct($questionnaireId, $options = null)
	{
		$this->_questionnaireId = $questionnaireId;
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
			$this->createElement('hidden', 'id', array(
				'required' => true,
			)),
			$this->createElement('hidden', 'questionnaire_id', array(
				'required' => true,
				'value' => $this->_questionnaireId,
			)),
			$this->createElement('text', 'filter', array(
				'label' => 'Filter:',
				'autocomplete' => 'off',
			)),
		));
	}
}