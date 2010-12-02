<?php

class QuestionnaireController extends Zend_Controller_Action
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
     * Renders the form for adding a questionnaire
     * 
     * @return void
     */
    public function addAction()
    {
    	$form = new HVA_Form_Questionnaire_Add();
    	
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$questionnaire = new Questionnaire();
    			$questionnaire->fromArray($data);
    			$questionnaire->save();
    			$this->_redirect('/');
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
    /**
     * Renders the form for editing a questionnaire
     * 
     * @return void
     */
    public function editAction()
    {
		$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
    	
		$form = new HVA_Form_Questionnaire_Edit($questionnaire);
    	
		if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$questionnaire->fromArray($data);
    			$questionnaire->save();
    			$this->_redirect('/');
    		}
    	}
		
    	$this->view->form = $form;
    	$this->view->questionnaire = $questionnaire;
    }
    
    /**
     * Renders the confirmation form for deleting a questionnaire
     * 
     * @return void
     */
    public function deleteAction()
    {
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
			
		$confirmationText = 'Weet u zeker dat u questionnaire ' . $questionnaire->id . ' (inclusief alle vragen en antwoorden) wilt verwijderen?';
			
    	$form = new HVA_Form_Confirm($questionnaire->id, $confirmationText);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		if ($this->_request->yes) {
    			$questionnaire->delete();
    		}
    		$this->_redirect('/');
    	}
    	
    	/* render view */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->view->form = $form;
    	$this->_response->setBody($this->view->render('confirm.phtml'));
    }
    
    /**
     * Renders the data collection for the given questionnaire
     * 
     * @return void
     */
    public function collectAction()
    {
    	/* get questionnaire */
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
			
		/* set respondent */
		if ($this->_request->respondent_id) {
			$respondent = Doctrine_Core::getTable('Respondent')
				->find($this->_request->respondent_id);
		} else {
			$respondent = new Respondent();
			$respondent->questionnaire_id = $questionnaire->id;
			$respondent->save();
		}
		
		/* build form */
		$form = new Zend_Form(array(
			'action' => $this->view->baseUrl('/questionnaire/collect/id/' . $this->_request->id)
		));
		$form->addElement($form->createElement('hidden', 'respondent_id', array('value' => $respondent->id)));
		$qqs = array();		
		foreach ($questionnaire->QuestionnaireQuestion as $qq) {
			$form->addElement($this->view->questionElement($qq));
			$qqs[$qq->id] = $qq;
		}
		$form->addElement($form->createElement('submit', 'submit', array('label' => 'verzenden')));
		
		/* process posted data */
		if ($this->_request->isPost()) {
			$data = $this->_request->getPost();
			if ($form->isValid($data)) {
				foreach ($data as $k => $v) {
					if (preg_match('/^qq_(\d*)$/', $k, $m)) {
						$answer = new Answer();
						$answer->questionnaire_question_id = $m[1];
						$answer->respondent_id = $respondent->id;
						if ($v) {
							if ($qqs[$m[1]]->answerPossibilityGroup_id) {
								$answer->answerPossibility_id = $v;
							} else {
								$answer->text = $v;
							}
							$answer->save();
						}
					}
				}
				$this->_helper->FlashMessenger->addMessage('Opgeslagen');
				$this->_redirect('/');
			}
		}
			
		/* display form */
		$this->view->form = $form;
    }

	/**
     * Renders the data collection for the given questionnaire
     * 
     * @return void
     */
    public function reportAction()
    {
    	/* get questionnaire */
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
			
		/* display */
		$this->view->questionnaire = $questionnaire;
		$this->view->language = $this->_language;
    }
}