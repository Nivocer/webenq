<?php

class InterpretationController extends Zend_Controller_Action
{
	/**
	 * Id of imported dataset
	 */
	protected $_id;
	
	
	/**
	 * Array of question objects
	 */
	protected $_questions = array();
	
	
	/**
	 * Initialisation
	 * 
	 * @return void
	 */
    public function init()
    {
    	$this->_id = $this->_request->id;
    	if (!$this->_id) {
    		throw new Exception("No id given!");
    	}
    }
	
    /**
     * Determines the question type for a given question
     * 
     * This actions dumps the result of the firts question,
     * or the questions with the given key. For debugging only.
     */
    public function debugAction()
    {
    	/* get table and its columns */
    	$table = new HVA_Model_DbTable_Data($this->_id);
    	$columns = $table->getColumns();
    	
    	/* test with one question (for debugging) */
    	$key = ($this->_request->key) ? $this->_request->key : 0;
    	if (!key_exists($key, $columns)) {
    		throw new Exception("Invalid key given!");
    	}
    	
    	/* dump question object */
    	$values = $table->fetchColumn($columns[$key]);
    	$question = HVA_Model_Data_Question::factory($values);
    	$this->_response->setBody(var_dump($question));
    	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * Determines question types based on available answers
     * 
     * @return void
     */
    public function indexAction()
    {
    	/* get questionnaire */
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')->find($this->_id);
    	var_dump($questionnaire); die;
    	
    	
    	
    	$columns = $table->getColumns();
    	
    	/* factor question objects */
    	foreach ($columns as $k => $v) {
    		$values = $table->fetchColumn($columns[$k]);
    		$this->_questions[$k] = HVA_Model_Data_Question::factory($values);
    	}
    	
    	/* create table */
    	HVA_Model_DbTable_Meta::createTable("meta_" . $this->_id);
    	
    	/* store meta information */
    	$meta = new HVA_Model_DbTable_Meta($this->_id);
    	foreach ($this->_questions as $k => $question) {
    		if (!is_object($question)) {
    			throw new Exception("Questions with index $k could not be detected!");
    		}
    		try {
    			$meta->insert(
	    			array(
		    			"question_id"	=> $columns[$k],
		    			"type"			=> get_class($question),
	    			)
		    	);
    		} catch(Zend_Db_Statement_Exception $e) {
    			throw $e;
    		}
    		
    		try {
		    	/* store other possible valid question types */
		    	$id = $meta->getAdapter()->lastInsertId();
		    	if ($id > 0) {
		    		foreach ($question->getValidTypes() as $validType) {
		    			$updated = $meta->insert(
			    			array(
			    				"parent_id"		=> $id,
				    			"question_id"	=> $columns[$k],
				    			"type"			=> $validType,
			    			)
				    	);
		    		}
		    	}
    		} catch(Zend_Db_Statement_Exception $e) {
    			throw $e;
    		}
    	}
    	
    	/* herschrijf waarden ivm volgorde rapportage */
    	$this->_rewriteValues();
    	
    	/* update status in imports table */
    	HVA_Model_DbTable_Imports::updateStatus($this->_id, HVA_Model_DbTable_Imports::INTERPRETED);
    	
    	$this->_redirect('index');
    }
    
    
    protected function _rewriteValues()
    {
    	$meta = new HVA_Model_DbTable_Meta($this->_id);
    	$data = new HVA_Model_DbTable_Data($this->_id);
    	
    	$percetageQuestions = $meta->fetchAll("type = 'HVA_Model_Data_Question_Closed_Percentage'");
    	foreach ($percetageQuestions as $percentageQuestion) {
    		$answers = $data->fetchAll($percentageQuestion->question_id . " LIKE '<%'");
    		if ($answers->count() > 0) {
    			foreach ($answers as $answer) {
    				$key = $percentageQuestion->question_id;
    				$val = str_replace("<", "0 - ", $answer->{$percentageQuestion->question_id});
    				$data->update(
    					array($key => $val),
    					"id = $answer->id"
    				);
    			}
    		}
    	}
    }
}