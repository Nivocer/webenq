<?php

class ManagementController extends Zend_Controller_Action
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
     * Renders the overview of export options
     */
    public function indexAction()
    {
    	/* get model, and query for meta-data */
    	$mtn = "meta_" . $this->_id;
    	$qtn = "questions_" . $this->_id;
    	$meta = new HVA_Model_DbTable_Meta($mtn);
    	
    	try {
    		$questionsMeta = $meta->fetchAll(
    			$meta->select()->setIntegrityCheck(false)
    				->from($mtn)
    				->joinInner($qtn, "$qtn.id = $mtn.question_id", array('question' => 'title'))
    				->where("parent_id = 0")
    				->order("$mtn.id")
    			);
    	}
    	catch (Zend_Db_Statement_Exception $e) {
    		if ($e->getCode() === "42S02") {
	    		die("De vragenlijst is nog niet geinterpreteerd.");
    		} else {    		
    			throw $e;
    		}
    	}
    	
    	/* get model for data-table */
    	$data = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	
    	/* factor question objects */
    	$q = array();
    	foreach ($questionsMeta as $key => $questionMeta) {
    		$q[$key]["question"] = utf8_encode($questionMeta->question);
    		$q[$key]["type"] = $questionMeta->type;
    		$q[$key]["validTypes"] = array();
    		$validTypes = $meta->fetchAll("parent_id = " . $questionMeta->id, "id");
    		foreach ($validTypes as $validType) {
    			$q[$key]["validTypes"][] = $validType->type;
    		}
    	}
    	
    	/* build form */
    	$form = new HVA_Form_Management($q);
    	
    	/* process posted values */
    	if ($this->getRequest()->isPost()) {
    		$this->_processManagement($this->getRequest()->getPost());
    		$this->_convertLabelsToValues();
    		$this->_redirect("index");
    	}
    	
    	/* assign vars to view */
    	$this->view->form = $form;
    }
    
    
    /**
     * Converts labels to values, based on question types
     */
    protected function _convertLabelsToValues()
    {
    	/* get table models */
    	$meta = new HVA_Model_DbTable_Meta("meta_" . $this->_id);
    	
    	/* query for building table */
    	$table = "values_" . $this->_id;
    	$q = "CREATE TABLE " . $table . " (
    		id INT NOT NULL, PRIMARY KEY (id), ";
    	
    	/* query for question types */
    	$questionTypes = $meta->fetchAll("parent_id = 0", "id");
    	foreach ($questionTypes as $questionType) {
    		switch ($questionType->type) {
    			case "HVA_Model_Data_Question_Open_Date":
    				$q .= $questionType->question_id . " DATETIME NOT NULL, ";
    				break;
    			case "HVA_Model_Data_Question_Closed_Scale_Two":
    			case "HVA_Model_Data_Question_Closed_Scale_Three":
    			case "HVA_Model_Data_Question_Closed_Scale_Four":
    			case "HVA_Model_Data_Question_Closed_Scale_Five":
    			case "HVA_Model_Data_Question_Closed_Scale_Six":
    			case "HVA_Model_Data_Question_Closed_Scale_Seven":
    				$q .= $questionType->question_id . " INT NOT NULL, ";
    				break;
    			default:
    				$q .= $questionType->question_id . " VARCHAR(256) NOT NULL, ";
    				break;
    		}
    	}
    	$q = substr($q, 0, -2) . ") DEFAULT CHARSET=utf8;";
    	
		/* get db connection */
    	$db = Zend_Db_Table::getDefaultAdapter();
    	$dbConnection = $db->getConnection();
    	$dbConnection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		
    	/* create table */
    	$dbConnection->exec("DROP TABLE IF EXISTS " . $table . ";");
    	$dbConnection->exec($q);
    	
    	/* get labels and store values */    	
    	$labels = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$values = new HVA_Model_DbTable_Data("values_" . $this->_id);
    	
    	foreach ($labels->fetchAll() as $row) {
    		$data = $row->toArray();
    		foreach ($data as $questionId => $answer) {
    			$type = $meta->fetchRow("question_id = '$questionId'");
    			if ($type) {
    				if (substr($type->type, 0, 35) === "HVA_Model_Data_Question_Open_Date") {
						foreach (HVA_Model_Data_Question_Open_Date::getValidFormats() as $format) {
							$validator = new Zend_Validate_Date($format);
							if ($validator->isValid($answer)) {
								$date = new Zend_Date($answer, $format);
								break;
							}
						}
						$data[$questionId] = $date->toString("Y-M-d H:m:s");
    				}
    				if (substr($type->type, 0, 37) === "HVA_Model_Data_Question_Closed_Scale_") {
    					$scaleValues = HVA_Model_Data_Question_Closed_Scale::getScaleValues();
    					@$value = $scaleValues[$type->type][strtolower($answer)];
    					if (!$value) $value = -1;
    					$data[$questionId] = $value;
    				}
    			}
    		}
    		$values->insert($data);
    	}
    }
    
    
    /**
     * Processes the posted values of the questions management form
     * 
     * @param array Posted values
     */
    protected function _processManagement(array $post = array())
    {
    	$meta = new HVA_Model_DbTable_Meta("meta_" . $this->_id);
    	
    	foreach ($post as $k => $v) {
    		/* get values that have been changed */
    		$rowOne = $meta->fetchRow("parent_id = 0 AND question_id = '$k' AND type != '$v'");
    		if ($rowOne) {
    			$rowTwo = $meta->fetchRow("parent_id = $rowOne->id AND question_id = '$k' AND type = '$v'");
    			$meta->update(
    				array("type" => $v),
    				"id = " . $rowOne->id
    			);
    			$meta->update(
    				array("type" => $rowOne->type),
    				"id = " . $rowTwo->id
    			);
    		}
    	}
    }
}