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
		$this->_question = ($this->_request->id) ? Doctrine_Core::getTable('Question')->find($this->_request->id) : null;
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
     * Renders the form for editing a question
     * 
     * @return void
     */
    public function editAction()
    {
    	$form = new HVA_Form_Question_Edit($this->_question);
    	$this->view->form = $form;
    }
}