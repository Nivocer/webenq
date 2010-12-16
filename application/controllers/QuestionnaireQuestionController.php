<?php

class QuestionnaireQuestionController extends Zend_Controller_Action
{
	/**
	 * Controller actions that are ajaxable
	 * 
	 * @var array
	 */
	public $ajaxable = array(
		'edit' => array('html'),
	);
	
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
		$this->_helper->ajaxContext()->initContext();
		
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
    			/**
    			 * On an ajax-submit nothing is stored. The form is just
    			 * returned with the updated values. This enabled the handling
    			 * of dependencies in the form.
    			 */
    			if ($this->_helper->ajaxContext()->getCurrentContext()) {
    				$form->populate($data);
    				$this->_response->setBody($form->render());
    				$this->_helper->viewRenderer->setNoRender();
    			} else {
    				$form->storeValues();
    				$this->_redirect('questionnaire/edit/id/' . $questionnaireQuestion->Questionnaire->id);
    			}
    		}
    	} else {
			/* remove answer possibility selection if checkbox not checked */
			if (!$form->useAnswerPossibilityGroup->isChecked()) {
				$form->removeElement('answerPossibilityGroup_id');
			}
    	}
		
    	$this->view->form = $form;
    	$this->view->questionnaireQuestion = $questionnaireQuestion;
    }
}