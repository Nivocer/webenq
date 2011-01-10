<?php
class HVA_Form_Questionnaire_Collect extends Zend_Form
{
	/**
	 * Collection of QuestionnaireQuestions
	 * 
	 * @var Doctrine_Collection containing instances of QuestionnaireQuestion
	 */
	protected $_questions;
	
	public function __construct(Doctrine_Collection $questions, $options = null)
	{
		$this->_questions = $questions;
		parent::__construct($options);
	}
	
	public function init()
	{
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
		
		/* iterate over questions */
		foreach ($this->_questions as $question) {
			
			/* get sub-questions */
			$subQuestions = QuestionnaireQuestion::getSubQuestions($question);
			
			/* if no sub-questions: add element */
			if ($subQuestions->count() == 0) {
				$this->addElement($view->questionElement($question, false));
			}
			
			/* if sub-questions: add subform */
			else {
				$subForm = new Zend_Form_SubForm();
				
				/* iterate over sub-questions */
				foreach ($subQuestions as $subQuestion) {
					
					/* get sub-sub-questions */
					$subSubQuestions = QuestionnaireQuestion::getSubQuestions($subQuestion);
					if ($subSubQuestions->count() > 0) {
						
						$subSubForm = new Zend_Form_SubForm();
						
						/* iterate over sub-sub-questions */
						foreach ($subSubQuestions as $subSubQuestion) {
							$subSubForm->addElement($view->questionElement($subSubQuestion, false));
						}
						$subForm->addSubForm($subSubForm, $subQuestion->Question->QuestionText[0]->text);
					}
				}
				$this->addSubForm($subForm, $question->Question->QuestionText[0]->text);
			}
		}
		
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'verder',
			))
		);
	}
}