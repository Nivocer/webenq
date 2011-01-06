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
     * Renders the overview of questoinnaires
     * 
     * @return void
     */
    public function indexAction()
    {
    	$this->view->questionnaires =
    		Doctrine_Core::getTable('Questionnaire')->findAll();
    }
	
	/**
     * Renders the form for adding a questionnaire
     * 
     * @return void
     */
    public function addAction()
    {
    	$this->_helper->actionStack('index', 'questionnaire');
    	
    	$form = new HVA_Form_Questionnaire_Add();
    	
    	if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$questionnaire = new Questionnaire();
    			$questionnaire->fromArray($data);
    			$questionnaire->save();
    			$this->_redirect('/questionnaire');
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
    			$this->_redirect($this->_request->getPathInfo());
    		}
    	}
		
		$totalPages = Doctrine_Query::create()
			->select('MAX(cp.page) as max')
			->from('QuestionnaireQuestion qq')
			->innerJoin('qq.CollectionPresentation cp')
			->where('qq.questionnaire_id = ?', $questionnaire->id)
			->execute()->getFirst()->max;
			
		$questions = Doctrine_Query::create()
			->from('QuestionnaireQuestion qq')
			->innerJoin('qq.CollectionPresentation cp')
			->where('qq.questionnaire_id = ?', $questionnaire->id)
			->andWhere('cp.parent_id IS NULL')
			->orderBy('cp.page, cp.weight, qq.id')
			->execute();
    	
		$repoQuestions = Doctrine_Query::create()
			->from('Question q')
			->leftJoin('q.QuestionnaireQuestion qq')
			->where('qq.id IS NULL')
			->execute();
    	
    	$this->view->form = $form;
    	$this->view->questionnaire = $questionnaire;
    	$this->view->totalPages = $totalPages;
    	$this->view->questions = $questions;
    	$this->view->repoQuestions = $repoQuestions;
    }
    
    public function orderAction()
    {
    	/* disable view/layout rendering */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->layout->disableLayout(true);
    	
    	$page = $this->_request->page;
    	$qqIds = $this->_request->qq;
    	
    	if (count($qqIds) == 0) return;
    	
    	$qqs = Doctrine_Query::create()
    		->from('QuestionnaireQuestion qq')
    		->innerJoin('qq.CollectionPresentation cp')
    		->whereIn('qq.id', $qqIds)
    		->execute();
    	
    	foreach ($qqs as $qq) {
    		
    		$weight = array_search($qq->id, $qqIds);
    		
    		if ($qq->CollectionPresentation[0]->weight != $weight ||
    			$qq->CollectionPresentation[0]->page != $page)
    		{
	    		$qq->CollectionPresentation[0]->weight = $weight;
    			$qq->CollectionPresentation[0]->page = $page;
	    		$qq->save();
    		}
    	}
    }
    
    public function addQuestionAction()
    {
		$qq = new QuestionnaireQuestion();
		$qq->questionnaire_id = $this->_request->questionnaire_id;
		$qq->question_id = $this->_request->question_id;
		$qq->CollectionPresentation[] = new CollectionPresentation();
		$qq->ReportPresentation[] = new ReportPresentation();
		$qq->save();
    	
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->layout->disableLayout(true);
    	$this->_response->setBody($qq->id);
    }
    
    /**
     * Renders the confirmation form for deleting a questionnaire
     * 
     * @return void
     */
    public function deleteAction()
    {
    	$this->_helper->actionStack('index', 'questionnaire');
    	
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
			
		$confirmationText = 'Weet u zeker dat u questionnaire ' . $questionnaire->id . ' (inclusief alle vragen en antwoorden) wilt verwijderen?';
			
    	$form = new HVA_Form_Confirm($questionnaire->id, $confirmationText);
    	
    	/* process posted data */
    	if ($this->_request->isPost()) {
    		if ($this->_request->yes) {
    			$questionnaire->delete();
    		}
    		$this->_redirect('/questionnaire');
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
    	/* get session */
    	$session = new Zend_Session_Namespace();
    	
    	/* reset respondent in session if other questionnaire */
    	if ($session->questionnaire_id != $this->_request->id) {
    		$session->respondent_id = null;
    	}
    	
    	/* set respondent */
		if ($this->_request->respondent_id) {
			$respondent = Doctrine_Core::getTable('Respondent')
				->find($this->_request->respondent_id);
		} else if ($session->respondent_id) {
			$respondent = Doctrine_Core::getTable('Respondent')
				->find($session->respondent_id);
		} else {
			$respondent = new Respondent();
			$respondent->questionnaire_id = $this->_request->id;
			$respondent->save();
		}
		
		/* get current page */
		$pages = Doctrine_Query::create()
			->from('QuestionnaireQuestion qq')
			->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
			->innerJoin('qq.CollectionPresentation cp')
			->where('a.id IS NULL')
			->andWhere('qq.questionnaire_id = ?', $this->_request->id)
			->orderBy('cp.page')
			->groupBy('cp.page')
			->limit(1)
			->execute();
			
		if ($pages) $firstPage = $pages->getFirst();
		if ($firstPage) $pageNr = $firstPage->CollectionPresentation->getFirst()->page;
		
		/* redirect if no more questions */
		if (!isset($pageNr)) $this->_redirect('/questionnaire');
		
		/* get questions for current page */
		$qqs = Doctrine_Query::create()
			->from('QuestionnaireQuestion qq')
			->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
			->innerJoin('qq.CollectionPresentation cp')
			->where('a.id IS NULL')
			->andWhere('qq.questionnaire_id = ?', $this->_request->id)
			->andWhere('cp.page = ?', $pageNr)
			->orderBy('cp.page, cp.weight, qq.id')
			->execute();
	
		/* redirect if no more questions */
		if (!$qqs) $this->_redirect('/questionnaire');
		
		/* get form */
		$form = new HVA_Form_Questionnaire_Collect($qqs);
		
		/* get progress data */
		$totalQuestions = Doctrine_Query::create()
			->from('QuestionnaireQuestion qq')
			->where('qq.questionnaire_id = ?', $this->_request->id)
			->execute()
			->count();
			
		$answeredQuestions = Doctrine_Query::create()
			->from('QuestionnaireQuestion qq')
			->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
			->where('a.id IS NOT NULL')
			->andWhere('qq.questionnaire_id = ?', $this->_request->id)
			->execute()
			->count();
		
		/* process posted data */
		if ($this->_request->isPost()) {
			if ($form->isValid($this->_request->getPost())) {
				foreach ($qqs as $qq) {
					
					/* get filtered and validated value(s) */
					$elm = $form->getElement('qq_' . $qq->id);
					$value = $elm->getValue();
					
					/* check for range */
					if (isset($this->_request->{$elm->getId() . '-1'})) {
						$value = array($value, $this->_request->{$elm->getId() . '-1'});
					}
					
					/* save answer-id(s) or text(s) */
					if ($qq->answerPossibilityGroup_id) {
						$this->_saveAnswerId($value, $qq, $respondent);
					} else {
						$this->_saveAnswerText($value, $qq, $respondent);
					}
				}
				
				/* store respondent id to session and reload page */
				$session->respondent_id = $respondent->id;
				$session->questionnaire_id = $this->_request->id;
				$this->_redirect($this->_request->getPathInfo());
			}
		}
			
		/* display form */
		$this->view->form = $form;
		$this->view->pageNr = $pageNr;
		$this->view->progress = array(
			'total' => $totalQuestions,
			'ready' => $answeredQuestions,
		);
    }
    
    protected function _saveAnswerId($value, QuestionnaireQuestion $qq, Respondent $respondent)
    {
    	try {
			if (is_array($value)) {
				foreach ($value as $answerPossibilityId) {
					$answer = new Answer();
					$answer->answerPossibility_id = $answerPossibilityId;
					$answer->respondent_id = $respondent->id;
					$answer->questionnaire_question_id = $qq->id;
					$answer->save();						
				}
			} else {
				$answer = new Answer();
				$answer->answerPossibility_id = $value;
				$answer->respondent_id = $respondent->id;
				$answer->questionnaire_question_id = $qq->id;
				$answer->save();						
			}
    	} catch(Exception $e) {
    		return false;
    	}
    	return true;
    }
    
    protected function _saveAnswerText($value, QuestionnaireQuestion $qq, Respondent $respondent)
    {
    	try {
			if (is_array($value)) {
				foreach ($value as $text) {
					$answer = new Answer();
					$answer->text = $text;
					$answer->respondent_id = $respondent->id;
					$answer->questionnaire_question_id = $qq->id;
					$answer->save();						
				}
			} else {
				$answer = new Answer();
				$answer->text = $value;
				$answer->respondent_id = $respondent->id;
				$answer->questionnaire_question_id = $qq->id;
				$answer->save();						
			}
    	} catch (Exception $e) {
    		return false;
    	}
    	return true;
    }
    
	/**
     * Renders the data collection for the given questionnaire
     * 
     * @return void
     */
    public function reportAction()
    {
    	$this->_response->setBody('De rapportfunctie is nog niet geïmplementeerd');
    	$this->_helper->viewRenderer->setNoRender(true);
    	return;
    	
    	/* get questionnaire */
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')
			->find($this->_request->id);
			
		/* display */
		$this->view->questionnaire = $questionnaire;
		$this->view->language = $this->_language;
    }
    
    public function groupAction()
    {
    	$questions = Doctrine_Query::create()
    		->from('CollectionPresentation cp')
    		->innerJoin('cp.QuestionnaireQuestion qq')
    		->innerJoin('qq.Questionnaire q')
    		->where('q.id = ?', $this->_request->id)
    		->andWhere('cp.parent_id IS NULL')
    		->groupBy('qq.id')
    		->execute();
    		
    	$groups = Doctrine_Query::create()
    		->from('CollectionPresentation cp')
    		->innerJoin('cp.QuestionnaireQuestion qq')
    		->innerJoin('qq.Questionnaire q')
    		->where('q.id = ?', $this->_request->id)
    		->andWhere('cp.parent_id IS NULL')
//    		->groupBy('cp.parent_id')
    		->execute();
    		
    	$this->view->questions = $questions;
    	$this->view->groups = $groups;
    }
}