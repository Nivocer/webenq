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
    	/* get data-set id */
    	$this->_id = $this->view->id = $this->getRequest()->getParam("id");
    	
    	if (!$this->_id) {
    		throw new Exception("No id given!");
    	}
    	
    	/* get title of data set */
    	$info = new HVA_Model_DbTable_Info('info_' . $this->_id);
    	$this->view->title = $info->getTitle();
    }
	
    public function indexAction()
    {
    	/* get models */
    	$data = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	
    	/* try to get existing report definitions */
    	try {
    		$repDefs = $this->view->reportDefinitions = $reportDefinitions->fetchAll(
    			$reportDefinitions->select()
    				->where("data_set_id = ?", $this->_id)
    				->order("id DESC")
    		);
    	} catch (Zend_Db_Statement_Exception $e) {
    		$reportDefinitions->createTable();
    	}
    }
    
    
    public function addAction()
    {
    	/* get models */
    	$data = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$questions = new HVA_Model_DbTable_Questions("questions_" . $this->_id);
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	
    	/* get enum options */
    	$outputFormats = $reportDefinitions->getEnumValues('output_format');
    	$reportTypes = $reportDefinitions->getEnumValues('report_type');
    	
    	/* get form */
    	$form = new HVA_Form_ReportDefinition($questions->getQuestions(), $outputFormats, $reportTypes);

    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
	    		$this->_processEdit();
	    		$this->_redirect("/report-definition/index/id/" . $this->_id);
    		}
    	}
    	
    	$this->view->form = $form;    	
    }
    
    
    public function editAction()
    {
    	/* get models */
    	$data = new HVA_Model_DbTable_Data("data_" . $this->_id);
    	$questions = new HVA_Model_DbTable_Questions("questions_" . $this->_id);
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	$repDef = $reportDefinitions->find($this->_request->getParam('report-definition-id'))->current();
    	
    	/* get enum options */
    	$outputFormats = $reportDefinitions->getEnumValues('output_format');
    	$reportTypes = $reportDefinitions->getEnumValues('report_type');
    	
    	/* get form */
    	$form = new HVA_Form_ReportDefinition($questions->getQuestions(), $outputFormats, $reportTypes);
    	$values = $repDef->toArray();
    	$values['ignore_question_ids'] = json_decode('[' . $values['ignore_question_ids'] . ']');
    	$form->populate($values);

    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
	    		$this->_processEdit();
	    		$this->_redirect("/report-definition/index/id/" . $this->_id);
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
    
    public function delAction()
    {
    	/* get form */
    	$form = new Zend_Form();
    	$confirm = new Zend_Form_Element_Submit('confirm');
    	$confirm->setLabel("ja, verwijderen")->setValue("yes");
    	$form->addElement($confirm);

    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
	    		$this->_processDel();
	    		$this->_redirect("/report-definition/index/id/" . $this->_id);
    		}
    	}
    	
    	$this->view->form = $form;
    }
    
    
    protected function _processEdit()
    {
    	/* get model */
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	
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
    	
    	/* get posted data */
    	$post = $this->getRequest()->getPost();
    	
    	/* set default file name if none set */
    	if (!$post["output_filename"]) {
    		$post["output_filename"] = md5(time());
    	}
    	
    	$cdlIgnoreQuestionIds = json_encode($post['ignore_question_ids']);
    	$cdlIgnoreQuestionIds = substr($cdlIgnoreQuestionIds, 1);
    	$cdlIgnoreQuestionIds = substr($cdlIgnoreQuestionIds, 0, -1);
    	
    	/* insert report definition */
    	if ($repDefId = $this->_request->getParam('report-definition-id')) {
	    	$reportDefinitions->update(
		    	array(
		    		"data_set_id"			=> $this->_id,
		    		"group_question_id"		=> $post["group_question_id"],
		    		"output_filename"		=> $post["output_filename"],
		    		"output_format"			=> $post["output_format"],
		    		"report_type"			=> $post["report_type"],
		    		"ignore_question_ids"	=> $cdlIgnoreQuestionIds,
		    	),
		    	"id = '" . $repDefId . "'");
    	} else {
	    	$reportDefinitions->insert(
		    	array(
		    		"data_set_id"			=> $this->_id,
		    		"group_question_id"		=> $post["group_question_id"],
		    		"output_filename"		=> $post["output_filename"],
		    		"output_format"			=> $post["output_format"],
		    		"report_type"			=> $post["report_type"],
		    		"ignore_question_ids"	=> json_encode($post['ignore_question_ids']),
		    	)
		    );
    	}
    }


    protected function _processDel()
    {
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	$reportDefinitions->delete("id = " . $this->getRequest()->getParam("report-definition-id"));
    }
}