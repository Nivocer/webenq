<?php
class HVA_Form_AnswerPossibility_Add extends Zend_Form
{
	/**
	 * Current answer-possibility-group
	 * 
	 * @var AnswerPossibilityGroup $_answerPossibilityGroup
	 */
	protected $_answerPossibilityGroup;
	
	/**
	 * Current language
	 * 
	 * @var string $_language
	 */
	protected $_language;
	
	/**
	 * Class constructor
	 * 
	 * @param AnswerPossibilityGroup $_answerPossibilityGroup
	 * @param string $language
	 * @param array|Zend_Config $options
	 * @return void
	 */
	public function __construct(AnswerPossibilityGroup $answerPossibilityGroup, $language, array $options = null)
	{
		$this->_answerPossibilityGroup = $answerPossibilityGroup;
		$this->_language = $language;

		parent::__construct($options);
	}
	
	/**
	 * Builds the form
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->addElements(array(
			$this->createElement('hidden', 'answerPossibilityGroup_id', array(
				'value' => $this->_answerPossibilityGroup->id,
			)),
			$this->createElement('select', 'language', array(
				'label' => 'Taal:',
				'multiOptions' => array(
					'nl' => 'nl',
				),
				'value' => $this->_language,
			)),
			$this->createElement('text', 'text', array(
				'label' => 'Tekst:',
				'required' => true,
			)),
			$this->createElement('text', 'value', array(
				'label' => 'Waarde:',
				'required' => true,
				'validators' => array(
					'Int',
				),
			)),
			$this->createElement('submit', 'submit', array(
				'label' => 'opslaan',
			)),
		));
	}
}