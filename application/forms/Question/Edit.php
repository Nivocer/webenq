<?php
class HVA_Form_Question_Edit extends HVA_Form_Question_Add
{
	/**
	 * Current question
	 * 
	 * @var Question
	 */
	protected $_question;
	
	/**
	 * Class constructor
	 * 
	 */
	public function __construct($question, $options = null)
	{
		$this->_question = $question;
		parent::__construct($options);
		$this->populate($this->_question->toArray());
	}
	
	/**
	 * Builds the form
	 * 
	 * @return void
	 */
	public function init()
	{
		parent::init();
		
		$id = $this->createElement('hidden', 'id', array(
			'value' => $this->_question->id,
		));
		$this->addElements(array($id));
	}
	
	/**
	 * Populates the form
	 * 
	 * @param array $values
	 * @return void
	 */
	public function populate(array $values)
	{
		parent::populate($values);
		
		$this->text->setValue($this->_question->QuestionText[0]->text);
	}
}