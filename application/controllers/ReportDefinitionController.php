<?php

class ReportDefinitionController extends Zend_Controller_Action
{
	/**
	 * Id of the data-set currently working with
	 */
	protected $_id;
	
	
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
	
    public function indexAction()
    {
    	$data = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$form = new HVA_Form_ReportDefinition($data->getColumns());
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	
    	/* get currently selected */
    	$this->view->reportDefinitions = $reportDefinitions->fetchAll("data_set_id = $this->_id");
    	
    	if ($q = $this->getRequest()->getPost('question')) {
    		$this->_processReportDefinition($form);
    		$this->_redirect("/");
    	}
    	
    	$this->view->form = $form;    	
    }
    
    
    protected function _processReportDefinition(Zend_Form $form)
    {
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	$form->populate($this->getRequest()->getPost());
    	
    	/* test if table exists */
		try {
    		$reportDefinitions->getDefaultAdapter()->describeTable($reportDefinitions->getName());
    	} catch(Exception $e) {
    		if ($e->getCode() === "42S02") {
    			$reportDefinitions->createTable();
    		} else {
    			throw $e;
    		}
    	}
    	
    	/* insert report definition */
    	$reportDefinitions->insert(array(
    		"data_set_id"		=> $this->_id,
    		"group_question_id"	=> $form->question->getValue()
    	));
    }
}