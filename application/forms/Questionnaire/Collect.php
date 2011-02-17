<?php
class HVA_Form_Questionnaire_Collect extends Zend_Form
{
	/**
	 * Collection of QuestionnaireQuestions
	 * 
	 * @var Doctrine_Collection containing instances of QuestionnaireQuestion
	 */
	protected $_questions;
	
	public function __construct(array $questions, $options = null)
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
			if (!isset($subQuestions[0])) {
				$this->addElement($view->questionElement($question, false));
			}
			
			/* if sub-questions: add subform */
			else {
				$subForm = new Zend_Form_SubForm();
				$subForm->setLegend($question['Question']['QuestionText'][0]['text'])
					->removeDecorator('DtDdWrapper');
					
				/* iterate over sub-questions */
				foreach ($subQuestions as $subQuestion) {
					
					/* get sub-sub-questions */
					$subSubQuestions = QuestionnaireQuestion::getSubQuestions($subQuestion);
					
					/* if no sub-sub-questions: add element */
					if (!isset($subSubQuestions[0])) {
						$subForm->addElement($view->questionElement($subQuestion, false));
					}
					
					/* if sub-sub-questions: add subform */
					else {						
						$subSubForm = new Zend_Form_SubForm();
						$subSubForm->setLegend($subQuestion['Question']['QuestionText'][0]['text'])
							->removeDecorator('DtDdWrapper');
							
						/* prepare wrapper decorator */
						$wrapper = new Zend_Form_Decorator_HtmlTag();
						$wrapper->setTag('div');							
						$percentage = floor(100/count($subSubQuestions));
						$wrapper->setOption('style', "float: left; width: $percentage%;");
						
						/* iterate over sub-sub-questions */
						foreach ($subSubQuestions as $subSubQuestion) {
							$elm = $view->questionElement($subSubQuestion, false);
							$elm->addDecorator(array('Wrapper' => $wrapper));
							$subSubForm->addElement($elm);
						}
						$subForm->addSubForm($subSubForm, $subQuestion['Question']['QuestionText'][0]['text']);
					}
				}
				$this->addSubForm($subForm, $question['Question']['QuestionText'][0]['text']);
			}
		}
		
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'verder',
			))
		);
	}
	
    /**
     * Retrieve a single element
     *
     * @param  string $name
     * @return Zend_Form_Element|null
     */
    public function getElement($name)
    {
    	$element = parent::getElement($name);
    	if ($element) {
    		return $element;
    	} else {
    		$subForms = $this->getSubForms();
    		foreach ($subForms as $subForm) {
    			$element = $subForm->getElement($name);
        		if ($element) {
        			return $element;
        		} else {
        			$subSubForms = $subForm->getSubForms();
        			foreach ($subSubForms as $subSubForm) {
        				$element = $subSubForm->getElement($name);
        				if ($element) {
        					return $element;
        				}
        			}
        		}
        	}
        }
        return null;
    }
}