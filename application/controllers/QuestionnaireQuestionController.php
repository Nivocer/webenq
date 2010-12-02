<?php

class QuestionnaireQuestionController extends Zend_Controller_Action
{
	/**
	 * Current language
	 * 
	 * @var string
	 */
	protected $_language;
	
	/**
	 * Initializes the class
	 * 
	 * @return void
	 */
	public function init()
	{
		$this->_language = ($this->_request->language) ? $this->_request->language : 'nl';
	}
	
    /**
     * Renders the form for editing a questionnaire
     * 
     * @return void
     */
    public function editAction()
    {
		$questionnaireQuestion = Doctrine_Core::getTable('QuestionnaireQuestion')
			->find($this->_request->id);
			
		$form = new HVA_Form_QuestionnaireQuestion_Edit($questionnaireQuestion);
    	
		if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$questionnaireQuestion->CollectionPresentation[0]->type = $data['collectionPresentationType'];
    			$questionnaireQuestion->CollectionPresentation[0]->required = $data['required'];
    			$questionnaireQuestion->ReportPresentation[0]->type = $data['reportPresentationType'];
    			$questionnaireQuestion->save();
    			$this->_redirect('questionnaire/edit/id/' . $questionnaireQuestion->Questionnaire->id);
    		}
    	}
		
    	$this->view->form = $form;
    	$this->view->questionnaireQuestion = $questionnaireQuestion;
    }
}