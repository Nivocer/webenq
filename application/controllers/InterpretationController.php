<?php

class InterpretationController extends Zend_Controller_Action
{
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
    	/* start session and get session id */
    	$this->_session = new Zend_Session_Namespace("webenq");
    	$this->_sessionId = Zend_Session::getId();
    }
	
	
    /**
     * Determines question types based on available answers
     */
    public function indexAction()
    {
    	/* get table and its columns */
    	$table = new HVA_Model_DbTable_Data("data_" . $this->_sessionId);
    	$columns = $table->getColumns();
    	
    	/* test with one question (for debugging) */
//    	$k = 16;
//    	$values = $table->fetchColumn($columns[$k]);
//    	$this->_questions[$k] = HVA_Model_Data_Question::factory($values);
//    	var_dump($this->_questions[$k], $values);
//    	die;
    	
    	/* factor question objects */
    	foreach ($columns as $k => $v) {
    		$values = $table->fetchColumn($columns[$k]);
    		$this->_questions[$k] = HVA_Model_Data_Question::factory($values);
    	}
    	
    	/* get db connection */
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$dbConnection = $db->getConnection();
    	$dbConnection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    	
    	/* build query for creating table */
    	$t = "meta_" . $this->_sessionId;
    	$q = "CREATE TABLE " . $t . " (
    			id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
    			parent_id INT NOT NULL,
    			question_id VARCHAR(64) NOT NULL,
    			type VARCHAR(256) NOT NULL
    		);";
    	
    	/* create table */
    	$dbConnection->exec("DROP TABLE IF EXISTS " . $t . ";");
    	$dbConnection->exec($q);
    	
    	/* store meta information */
    	$meta = new HVA_Model_DbTable_Meta("meta_" . $this->_sessionId);
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
    			// error handling
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
    			// error handling
    		}
    	}
    	
    	$this->_redirect('index');
    }
}