<?php
class Webenq_Form_AnswerPossibility_Edit extends Zend_Form
{
	/**
	 * Current answer-possibility
	 * 
	 * @var AnswerPossibility $_answerPossibility
	 */
	protected $_answerPossibility;
	
	/**
	 * Array of answer-possibility-groups
	 * 
	 * @var array $_answerPossibilityGroups
	 */
	protected $_answerPossibilityGroups;
	
	/**
	 * Class constructor
	 * 
	 * @param AnswerPossibility $answerPossibility
	 * @param array|Zend_Config $options
	 * @return void
	 */
	public function __construct(AnswerPossibility $answerPossibility, array $options = null)
	{
		$this->_answerPossibility = $answerPossibility;
		
		$groups = Doctrine_Query::create()
			->from('AnswerPossibilityGroup apg')
			->orderBy('apg.name')
			->execute();
		foreach ($groups as $group) {
			$this->_answerPossibilityGroups[$group->id] = $group->name;
		}
		
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
			$this->createElement('hidden', 'id', array(
				'value' => $this->_answerPossibility->id,
			)),
			$this->createElement('select', 'language', array(
				'label' => 'Taal:',
				'multiOptions' => array(
					'nl' => 'nl',
				),
				'value' => $this->_answerPossibility->AnswerPossibilityText[0]->language,
			)),
			$this->createElement('text', 'text', array(
				'label' => 'Tekst:',
				'value' => $this->_answerPossibility->AnswerPossibilityText[0]->text,
				'required' => true,
			)),
			$this->createElement('text', 'value', array(
				'label' => 'Waarde:',
				'value' => $this->_answerPossibility->value,
				'required' => true,
				'validators' => array(
					'Int',
				),
			)),
			$this->createElement('select', 'answerPossibilityGroup_id', array(
				'label' => 'Groep:',
				'value' => $this->_answerPossibility->answerPossibilityGroup_id,
				'multiOptions' => $this->_answerPossibilityGroups,
			)),
			$this->createElement('submit', 'submit', array(
				'label' => 'opslaan',
			)),
		));
	}
}