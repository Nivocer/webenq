<?php

class AnswerPossibilityController extends Zend_Controller_Action
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
		$this->_language = Zend_Registry::get('language');
	}
	
    /**
     * Handles the adding of an answer-possibility
     * 
     * @return void
     */
    public function addAction()
    {
    	/* get group */
    	$answerPossibilityGroup = Doctrine_Core::getTable('AnswerPossibilityGroup')
    		->find($this->_request->id);
    		
    	/* get form */
    	$form = new Webenq_Form_AnswerPossibility_Add(
    		$answerPossibilityGroup,
    		$this->_language
    	);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			
   				$answerPossibilityText = new AnswerPossibilityText();
   				$answerPossibilityText->fromArray($data);
    			
    			$answerPossibility = new AnswerPossibility();
   				$answerPossibility->fromArray($data);
    			$answerPossibility->AnswerPossibilityText[] = $answerPossibilityText;
    			
    			try {
   					$answerPossibility->save();
    				$this->_redirect('/answer-possibility-group/edit/id/' . $answerPossibilityGroup->id);
    			}
    			catch (Exception $e) {
   					$form->value->addError($e->getMessage());
    			}
    		}
    	}
    	
    	/* render view */
    	$this->view->form = $form;
    	$this->view->answerPossibilityGroup = $answerPossibilityGroup;
    }
    
    /**
     * Handles the editing of an answer-possibility
     * 
     * @return void
     */
    public function editAction()
    {
    	/* get possibility */
    	$answerPossibility = Doctrine_Core::getTable('AnswerPossibility')
    		->find($this->_request->id);
    		
    	/* get form */
    	$form = new Webenq_Form_AnswerPossibility_Edit($answerPossibility);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			
    			$errors = array();
    			
    			/* store possibility */
    			$answerPossibility->fromArray($data);
    			try {
    				$answerPossibility->save();
    			}
    			catch (Exception $e) {
    				$errors[] = $e->getMessage();
    			}    			
    			
    			/* store text */
    			$answerPossibilityText = Doctrine_Core::getTable('AnswerPossibilityText')
    				->findOneByAnswerPossibility_idAndLanguage($answerPossibility->id, $this->_language);
    				
    			if (!$answerPossibilityText) {
    				$answerPossibilityText = new AnswerPossibilityText();
    				$answerPossibilityText->language = $this->language;
    				$answerPossibilityText->answerPossibility_id = $answerPossibility->id;
    			}
    			
    			$answerPossibilityText->text = $data['text'];
    			
    			try {
    				$answerPossibilityText->save();
    			}
    			catch (Exception $e) {
    				$errors[] = $e->getMessage();
    			}

    			if (count($errors) == 0) {
    				$this->_redirect('/answer-possibility-group/edit/id/' . $answerPossibility->AnswerPossibilityGroup->id);
    			} else {
    				$form->value->addErrors($errors);
    			}
    		}
    	}
    	
    	/* assign to view */
    	$this->view->form = $form;
    	$this->view->answerPossibility = $answerPossibility;
    }
    
    /**
     * Handles the deleting of an answer-possibility
     * 
     * @return void
     */
    public function deleteAction()
    {
    	/* get group */
    	$answerPossibility = Doctrine_Query::create()
    		->from('AnswerPossibility ap')
    		->innerJoin('ap.AnswerPossibilityText apt')
    		->where('ap.id = ?', $this->_request->id)
    		->andWhere('apt.language = ?', $this->_language)
    		->execute()
    		->getFirst();
    		
    	/* get form */
    	$form = new Webenq_Form_Confirm(
    		$answerPossibility->id,
    		'Weet u zeker dat u het antwoord "' . $answerPossibility->AnswerPossibilityText[0]->text . '" wilt verwijderen?'
    	);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		if ($this->_request->yes) {
    			$answerPossibility->delete();
    		}
    		$this->_redirect('/answer-possibility-group');
    	}
    	
    	/* render view */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->view->form = $form;
    	$this->_response->setBody($this->view->render('confirm.phtml'));
    }
}