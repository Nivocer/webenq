<?php

class QuestionController extends Zend_Controller_Action
{
	/**
	 * Current question
	 * 
	 * @var Question
	 */
	protected $_question;
	
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
     * Renders the overview of question types
     * 
     * @return void
     */
    public function indexAction()
    {
    	/* get questionnaire */
    	$questions = Doctrine_Query::create()
    		->from('Question q')
    		->innerJoin('q.QuestionText qt')
    		->where('qt.language = ?', $this->_language)
    		->orderBy('qt.text')
    		->execute();
    	$this->view->questions = $questions;
    }
    
    /**
     * Renders the form for adding a question
     * 
     * @return void
     */
    public function addAction()
    {
    	$form = new HVA_Form_Question_Add();
    	
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$question = new Question();
    			$question->QuestionText[0]->fromArray($data);
    			$question->save();
    			$this->_redirect('/question');
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
    /**
     * Renders the form for editing a question
     * 
     * @return void
     */
    public function editAction()
    {
		$question = Doctrine_Core::getTable('Question')
			->find($this->_request->id);
			
		$collectionPresentation = Doctrine_Core::getTable('CollectionPresentation')
			->findOneByquestionGroup_idAndQuestionGroup_questionnaire_question_id(
				$question->id,
				$question->QuestionnaireQuestion->id
			);
			
		$form = new HVA_Form_Question_Edit($question, $collectionPresentation);
    	
		if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$question->QuestionText[0]->fromArray($data);
    			$question->save();
    			$this->_redirect('/question');
    		}
    	}
		
    	$this->view->form = $form;
    }
    
    /**
     * Renders the confirmation form for deleting a question
     * 
     * @return void
     */
    public function deleteAction()
    {
    	$question = Doctrine_Core::getTable('Question')
			->find($this->_request->id);
			
		$confirmationText = 'Weet u zeker dat u de vraag "' . $question->QuestionText[0]->text . '" (inclusief alle vertalingen) wilt verwijderen?';
			
    	$form = new HVA_Form_Confirm($question->id, $confirmationText);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		if ($this->_request->yes) {
    			$question->delete();
    		}
    		$this->_redirect('/question');
    	}
    	
    	/* render view */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->view->form = $form;
    	$this->_response->setBody($this->view->render('confirm.phtml'));
    }
}