<?php

class ManagementController extends Zend_Controller_Action
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
    	$meta = new HVA_Model_DbTable_Meta();
    	$questionsMeta = $meta->fetchAll(
    		"parent = 0 AND tablename = '" . $dbTableName . "'",
    		"id");
    	$data = new HVA_Model_DbTable_Data($dbTableName);
    	
    	/* factor question objects */
    	foreach ($questionsMeta as $key => $questionMeta) {
    		
    		$q[$key]["question"] = $questionMeta->question;
    		$q[$key]["type"] = $questionMeta->type;
    		
    		$q[$key]["validTypes"] = array();
    		$validTypes = $meta->fetchAll("parent = " . $questionMeta->id);
    		foreach ($validTypes as $validType) {
    			$q[$key]["validTypes"][] = $validType->type;
    		}
    	}
    	
    	/* build form */
    	$form = new HVA_Form_Management($q);
    	
    	/* process posted values */
    	if ($this->getRequest()->isPost()) {
    		$this->_processManagement($this->getRequest()->getPost());
    		$this->_redirect("index");
    	}
    	
    	/* assign vars to view */
    	$this->view->form = $form;
    }
    
    
    /**
     * Processes the posted values of the questions management form
     * 
     * @param array Posted values
     */
    protected function _processManagement(array $post = array())
    {
    	$meta = new HVA_Model_DbTable_Meta();
    	
    	foreach ($post as $k => $v) {
    		/* get values that have been changed */
    		$rowOne = $meta->fetchRow("parent = 0 AND question = '$k' AND type != '$v'");
    		if ($rowOne) {
    			$rowTwo = $meta->fetchRow("parent = $rowOne->id AND question = '$k' AND type = '$v'");
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