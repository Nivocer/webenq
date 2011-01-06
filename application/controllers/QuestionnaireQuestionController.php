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
		'add-subquestion' => array('html'),
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
    	$this->view->cols = $cols = 1 + $questionnaireQuestion->CollectionPresentation[0]
    		->CollectionPresentation[0]->CollectionPresentation->count();
    	$this->view->subQuestions = $this->_getSubQuestions($questionnaireQuestion);
    }
    
    protected function _getSubQuestions(QuestionnaireQuestion $questionnaireQuestion)
    {
    	$subQuestions = array();
    	foreach ($questionnaireQuestion->CollectionPresentation->getFirst()->CollectionPresentation as $subQuestion) {
			if ($subQuestion->QuestionnaireQuestion->Question->QuestionText->count() > 0) {
				$subQuestions[$subQuestion->weight][0] = $subQuestion->QuestionnaireQuestion->Question->QuestionText[0];
				foreach ($subQuestion->CollectionPresentation as $subSubQuestion) {
					$subQuestions[$subQuestion->weight][$subSubQuestion->weight] = $subSubQuestion->QuestionnaireQuestion->Question->QuestionText[0];
				}
			}
    	}
    	
    	/* sort recursively */
    	ksort($subQuestions);
    	foreach ($subQuestions as $array) {
    		ksort($array);
    	}
		
		return $subQuestions;
    }
    
    public function saveStateAction()
    {
    	/* disable view/layout rendering */
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->layout->disableLayout(true);
    	
    	$cols = $this->_request->cols;
    	$qqIds = is_array($this->_request->qq) ? array_unique($this->_request->qq) : array();
    	$parentId = Doctrine_Core::getTable('QuestionnaireQuestion')
    		->find($this->_request->parent)
    		->CollectionPresentation[0]->id;
    		
    	/* reset all for this parent */
    	Doctrine_Query::create()
    		->update('CollectionPresentation')
    		->set('parent_id', '?', '')
    		->where('parent_id = ?', $parentId)
    		->execute();
    	
    	$i = 0;
    	$row = array();
    	$rows = array();
    	foreach ($qqIds as $j => $qqId) {
    		$i++;
    		$row[] = $qqId;
    		if ($i == $cols || $j == count($qqIds) - 1) {
    			$rows[] = $row;
    			$row = array();
    			$i = 0;
    		}
    	}
    	
    	foreach ($rows as $i => $row) {
    		foreach ($row as $j => $col) {
    			
    			/* get questionnaire-question */
		    	$qq = Doctrine_Core::getTable('QuestionnaireQuestion')
		    		->find($col);
		    		
    			/* reset all for this parent */
		    	Doctrine_Query::create()
		    		->update('CollectionPresentation')
		    		->set('parent_id', '?', '')
					->set('weight', '?', '0')
		    		->where('parent_id = ?', $qq->CollectionPresentation[0]->id)
		    		->execute();
    			
    			/* save new state */
		    	if ($j == 0) {
		    		$qq->CollectionPresentation[0]->parent_id = $parentId;
		    	} else {
		    		$qq->CollectionPresentation[0]->parent_id = Doctrine_Core::getTable('QuestionnaireQuestion')
		    			->find($row[0])->CollectionPresentation[0]->id;
		    	}    			
		    	$qq->CollectionPresentation[0]->weight = $i * $i + $j;
		    	$qq->save();
    		}
    	}
    }
    
    public function addSubquestionAction()
    {
    	$qq = Doctrine_Query::create()
    		->from('QuestionnaireQuestion qq')
    		->innerJoin('qq.CollectionPresentation cp')
    		->where('qq.id != ?', $this->_request->id)
    		->andWhere('cp.parent_id IS NULL')
    		->execute();
    		
    	$this->view->qq = $qq;
    }
}