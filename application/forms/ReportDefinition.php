<?php

class HVA_Form_ReportDefinition extends Zend_Form
{
	/**
	 * Questions
	 */
	protected $_questions = array();
	
	
	/**
	 * Class constructor
	 * 
	 * @param array $questions
	 * @param array $options Zend_Form options
	 */
	public function __construct(array $questions, $options = null) {
		parent::__construct($options);
		$this->_questions = $questions;
		$this->_buildForm();
	}


	/**
	 * Builds the form
	 */
	protected function _buildForm()
	{
		/* needed to show the default checked radio button in FireFox */
		$this->setAttrib("autocomplete", "off");
		
    	$select = new Zend_Form_Element_Radio('question');
    	$select
    		->setLabel('Selecteer een vraag om de data te groeperen:')
    		->setMultiOptions($this->_questions);
    	
    	$submit = new Zend_Form_Element_Submit('submit');
    	$submit->setLabel('Verzenden');
    	
    	$this->addElements(array($select, $submit));
	}
}