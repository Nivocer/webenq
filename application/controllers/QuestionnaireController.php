<?php

class QuestionnaireController extends Zend_Controller_Action
{
	/**
	 * Controller actions that are ajaxable
	 * 
	 * @var array
	 */
	public $ajaxable = array(
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
		$this->_language = Zend_Registry::get('language');
	}
	
	/**
     * Renders the overview of questoinnaires
     * 
     * @return void
     */
    public function indexAction()
    {
    	$this->view->questionnaires = Doctrine_Query::create()
    		->select('q.*, COUNT(qq.id) as count_qqs')
    		->from('Questionnaire q')
    		->leftJoin('q.QuestionnaireQuestion qq')
    		->groupBy('q.id')
    		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
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
    			$questionnaire->meta = serialize(array('timestamp' => time()));
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
    	$questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language);
    	
		$form = new HVA_Form_Questionnaire_Edit($questionnaire);
		if ($this->_request->isPost()) {
    		$data = $this->_request->getPost();
    		if ($form->isValid($data)) {
    			$questionnaire->fromArray($data);
    			$questionnaire->save();
    			$this->_redirect($this->_request->getPathInfo());
    		}
    	}
    	
    	$this->view->form = $form;
    	$this->view->questionnaire = $questionnaire;
    	$this->view->totalPages = Questionnaire::getTotalPages($questionnaire['id']);
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
		$cp = new CollectionPresentation();
		$cp->weight = -1;
		$qq->CollectionPresentation[] = $cp;
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
		
		/* store respondent id to session and reload page */
		$session->respondent_id = $respondent->id;
		$session->questionnaire_id = $this->_request->id;
    	
		try {
			/* get current page */
			$pageNr = Doctrine_Query::create()
				->from('QuestionnaireQuestion qq')
				->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
				->innerJoin('qq.CollectionPresentation cp')
				->where('a.id IS NULL')
				->andWhere('qq.questionnaire_id = ?', $this->_request->id)
				->orderBy('cp.page')
				->groupBy('cp.page')
				->limit(1)
				->execute()
				->getFirst()->CollectionPresentation[0]->page;
		} catch (Exception $e) {
			/* redirect if no more questions */
			$this->_redirect('/questionnaire');
		}
		
		/* get questions for current page */
    	$questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language, $pageNr, $respondent);
    	$qqs = $questionnaire->QuestionnaireQuestion;
    	
		/* redirect if no more questions */
		if ($qqs->count() == 0) $this->_redirect('/questionnaire');
		
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
					$value = '';
					$elm = $form->getElement('qq_' . $qq->id);
					if (is_object($elm)) {
						/* get value */
						$value = $elm->getValue();
						/* check for range */
						if (isset($this->_request->{$elm->getId() . '-1'})) {
							$value = array($value, $this->_request->{$elm->getId() . '-1'});
						}
					}
					
					/* save answer-id(s) or text(s) */
					if ($qq->answerPossibilityGroup_id) {
						$this->_saveAnswerId($value, $qq, $respondent);
					} else {
						$this->_saveAnswerText($value, $qq, $respondent);
					}
				}
				
				/* reload page */
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
     * Reports the collected data for the given questionnaire
     * 
     * @return void
     */
    public function reportAction()
    {
		/* get questions for current page */
    	$pageNr = $this->_request->page ? $this->_request->page : null;
    	$questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language, $pageNr);
			
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