<?php

class InterpretationController extends Zend_Controller_Action
{
	/**
	 * Array of question objects
	 */
	protected $_questions = array();
	
	
    /**
     * Renders the overview of export options
     */
    public function indexAction()
    {
    	/* get db-table name from session */
    	$session = new Zend_Session_Namespace("webenq");
    	$dbTableName = $session->dbTableName;
    	
    	/* get table and its columns */
    	$table = new HVA_Model_DbTable_Data($dbTableName);
    	$columns = $table->getColumns();
    	
    	/* factor question objects */
    	foreach ($columns as $k => $v) {
    		$values = $table->fetchColumn($columns[$k]);
    		$this->_questions[$k] = HVA_Model_Data_Question::factory($values);
    	}
    	
    	/* store meta information */
    	$meta = new HVA_Model_DbTable_Meta();
    	$meta->delete("tablename = '" . $dbTableName . "'");
    	foreach ($this->_questions as $k => $question) {
    		if (!is_object($question)) {
    			throw new Exception('Question could not be detected!');
    		}
    		try {
    			$meta->insert(
	    			array(
		    			"tablename"		=> $dbTableName,
		    			"question"	=> $columns[$k],
		    			"type"		=> get_class($question),
    					"time" => time()
	    			)
		    	);
    		} catch(Zend_Db_Statement_Exception $e) {
    			// error handling
    		}
    		
    		try {
		    	/* store other possible valid question types */
		    	$id = $meta->getAdapter()->lastInsertId();
		    	if ($id > 0) {
		    		foreach ($question->getValidTypes() as $validType) {
		    			$updated = $meta->insert(
			    			array(
			    				"parent"		=> $id,
				    			"tablename"		=> $dbTableName,
				    			"question"		=> $columns[$k],
				    			"type"			=> $validType,
		    					"time" 			=> time()
			    			)
				    	);
		    		}
		    	}
    		} catch(Zend_Db_Statement_Exception $e) {
    			// error handling
    		}
    	}
    	
    	$this->_redirect('index');
    }
}