<?php

class AnswerPossibilityGroupController extends Zend_Controller_Action
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
     * Renders the overview of question types
     * 
     * @return void
     */
    public function indexAction()
    {
    	/* get answer possibility groups */
    	$answerPossibilityGroups = Doctrine_Query::create()
    		->from('AnswerPossibilityGroup apg')
    		->orderBy('apg.name')
    		->execute();
    		
    	/* render view */
    	$this->view->answerPossibilityGroups = $answerPossibilityGroups;
    }
    
    /**
     * Handles the adding of an answer-possibility-group
     * 
     * @return void
     */
    public function addAction()
    {
    	/* get form */
    	$form = new HVA_Form_AnswerPossibilityGroup_Add();
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$answerPossibilityGroup = new AnswerPossibilityGroup();
    			$answerPossibilityGroup->fromArray($data);
    			$answerPossibilityGroup->save();
    			$this->_redirect('/answer-possibility-group');
    		}
    	}
    	
    	/* render view */
    	$this->view->form = $form;
    }
    
    /**
     * Handles the editing of an answer-possibility-group
     * 
     * @return void
     */
    public function editAction()
    {
    	/* get group */
    	$answerPossibilityGroup = Doctrine_Query::create()
    		->from('AnswerPossibilityGroup apg')
    		->innerJoin('apg.AnswerPossibility ap')
    		->where('apg.id = ?', $this->_request->id)
    		->orderBy('ap.value')
    		->execute()
    		->getFirst();
    		
    	/* get form */
    	$form = new HVA_Form_AnswerPossibilityGroup_Edit($answerPossibilityGroup);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$answerPossibilityGroup->fromArray($data);
    			$answerPossibilityGroup->save();
    			$this->_redirect('/answer-possibility-group');
    		}
    	}
    	
    	/* render view */
    	$this->view->form = $form;
    	$this->view->answerPossibilityGroup = $answerPossibilityGroup;
    }
    
    /**
     * Handles the deleting of an answer-possibility-group
     * 
     * @return void
     */
    public function deleteAction()
    {
    	$this->_helper->actionStack('index', 'answer-possibility-group');
    	
    	/* get group */
    	$answerPossibilityGroup = Doctrine_Core::getTable('AnswerPossibilityGroup')
    		->find($this->_request->id);
    		
    	/* get form */
    	$form = new HVA_Form_Confirm(
    		$answerPossibilityGroup->id,
    		'Weet u zeker dat u antwoordengroep "' . $answerPossibilityGroup->name . '" wilt verwijderen?'
    	);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		if ($this->_request->yes) {
    			$answerPossibilityGroup->delete();
    		}
    		$this->_redirect('/answer-possibility-group');
    	}
    	
    	/* render view */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->view->form = $form;
    	$this->_response->setBody($this->view->render('confirm.phtml'));
    }
}