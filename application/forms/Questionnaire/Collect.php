<?php
class HVA_Form_Questionnaire_Collect extends Zend_Form
{
	/**
	 * Collection of QuestionnaireQuestions
	 * 
	 * @var Doctrine_Collection
	 */
	protected $_qqs;
	
	public function __construct(Doctrine_Collection $qqs, $options = null)
	{
		$this->_qqs = $qqs;
		parent::__construct($options);
	}
	
	public function init()
	{
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
		
		foreach ($this->_qqs as $qq) {
			$this->addElement($view->questionElement($qq));
		}
		
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'verder',
			))
		);
	}
}