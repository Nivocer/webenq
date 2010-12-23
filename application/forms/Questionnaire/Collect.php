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
			if ($qq->CollectionPresentation[0]->CollectionPresentation->count() > 0) {
				$subForm = new Zend_Form_SubForm();
				foreach ($qq->CollectionPresentation[0]->CollectionPresentation as $cp) {
					if ($cp->CollectionPresentation->count() > 0) {
						$subSubForm = new Zend_Form_SubForm();
						foreach ($cp->CollectionPresentation as $subCp) {
							$subSubForm->addElement($view->questionElement($subCp->QuestionnaireQuestion, false));
						}
						$subForm->addSubForm($subSubForm, $cp->QuestionnaireQuestion->Question->QuestionText[0]->text);
					}
				}
				$this->addSubForm($subForm, $subCp->QuestionnaireQuestion->Question->QuestionText[0]->text);
			}
			$this->addElement($view->questionElement($qq, false));
		}
		
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'verder',
			))
		);
	}
}