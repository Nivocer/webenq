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
    	$this->_id = $this->getRequest()->getParam("id");
    	
    	if (!$this->_id) {
    		throw new Exception("No id given!");
    	}
    }
	
	
    /**
     * Determines question types based on available answers
     */
    public function indexAction()
    {
    	/* get table and its columns */
    	$table = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$columns = $table->getColumns();
    	
    	/* test with one question (for debugging) */
//    	$k = 46;
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
    	$t = "meta_" . $this->_id;
    	$q = "CREATE TABLE " . $t . " (
    			id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id),
    			parent_id INT NOT NULL,
    			question_id VARCHAR(64) NOT NULL,
    			type TEXT NOT NULL
    		) DEFAULT CHARSET=utf8;";
    	
    	/* create table */
    	$dbConnection->exec("DROP TABLE IF EXISTS " . $t . ";");
    	$dbConnection->exec($q);
    	
    	/* store meta information */
    	$meta = new HVA_Model_DbTable_Meta("meta_" . $this->_id);
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
    	
    	/* herschrijf waarden ivm volgorde rapportage */
    	$this->_rewriteValues();
    	
    	$this->_redirect('index');
    }
    
    
    protected function _rewriteValues()
    {
    	$meta = new HVA_Model_DbTable_Meta("meta_" . $this->_id);
    	$data = new HVA_Model_DbTable_Meta("data_" . $this->_id);
    	
    	$percetageQuestions = $meta->fetchAll("type LIKE 'HVA_Model_Data_Question_Closed_Percentage'");
    	foreach ($percetageQuestions as $percentageQuestion) {
    		$answers = $data->fetchAll($percentageQuestion->question_id . " LIKE '<%'");
    		if ($answers->count() > 0) {
    			foreach ($answers as $answer) {
    				$key = $percentageQuestion->question_id;
    				$val = str_replace("<", "0 - ", $answer->{$percentageQuestion->question_id});
    				$data->update(array($key => $val), "id = " . $answer->id);
    			}
    		}
    	}
    }
}