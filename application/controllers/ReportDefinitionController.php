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
    	$info = new Webenq_Model_DbTable_Info('info_' . $this->_id);
    	$this->_title = $this->view->title = $info->getTitle();
    }
	
    public function indexAction()
    {
    	/* get models */
    	$data = new Webenq_Model_DbTable_Data("data_" . $this->_id);
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	
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
    	
    	/* make two default report definitions if none present */
    	if ($repDefs->count() === 0) {
    		$this->_createDefaultReportDefinitions();
	    	$this->view->reportDefinitions = $reportDefinitions->fetchAll(
	    		$reportDefinitions->select()
	    			->where("data_set_id = ?", $this->_id)
	    			->order("id DESC")
	    	);
    	}
    }
    
    protected function _createDefaultReportDefinitions()
    {
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	$questions = new Webenq_Model_DbTable_Questions("questions_" . $this->_id);
    	
    	$leewenburgDefaultGroupRow = $questions->fetchAll("title = 'basisgroep'");
    	if ($leewenburgDefaultGroupRow->count() === 1) {
    		$leewenburgDefaultGroupTitle = $leewenburgDefaultGroupRow->current()->id;
    	} else {
    		$leewenburgDefaultGroupTitle = '';
    	}
    	
    	$fraijlemaborgDefaultSplitRow = $questions->fetchAll("title = 'docent'");
    	if ($fraijlemaborgDefaultSplitRow->count() === 1) {
    		$fraijlemaborgDefaultSplitTitle = $fraijlemaborgDefaultSplitRow->current()->id;
    	} else {
    		$fraijlemaborgDefaultSplitTitle = '';
    	}
    	
    	$reportDefinitions->insert(
	    	array(
	    		"data_set_id"			=> $this->_id,
	    		"group_question_id"		=> $leewenburgDefaultGroupTitle,
	    		"output_filename"		=> str_replace(' ', '_', $this->_title) . '_open',
	    		"output_format"			=> 'pdf',
	    		"report_type"			=> 'open',
	    		"ignore_question_ids"	=> '"0_respondent","1_datum"',
	    		"language"				=> 'nl',
	    		"customer"				=> 'leeuwenburg',
	    		"page"					=> 'portrait',
	    	)
	    );
    	$reportDefinitions->insert(
	    	array(
	    		"data_set_id"			=> $this->_id,
	    		"group_question_id"		=> $leewenburgDefaultGroupTitle,
	    		"output_filename"		=> str_replace(' ', '_', $this->_title) . '_tables',
	    		"output_format"			=> 'pdf',
	    		"report_type"			=> 'tables',
	    		"ignore_question_ids"	=> '"0_respondent","1_datum"',
	    		"language"				=> 'nl',
	    		"customer"				=> 'leeuwenburg',
	    		"page"					=> 'portrait',
	    	)
	    );
    }
    
    
    public function addAction()
    {
    	/* get models */
    	$data = new Webenq_Model_DbTable_Data("data_" . $this->_id);
    	$questions = new Webenq_Model_DbTable_Questions("questions_" . $this->_id);
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	
    	/* get enum options */
    	$outputFormats = $reportDefinitions->getEnumValues('output_format');
    	$reportTypes = $reportDefinitions->getEnumValues('report_type');
    	$languages = $reportDefinitions->getEnumValues('language');
    	$customers = $reportDefinitions->getEnumValues('customer');
    	$pages = $reportDefinitions->getEnumValues('page');
    	
    	/* get form */
    	$form = new Webenq_Form_ReportDefinition($questions->getQuestions(), $outputFormats, $reportTypes, $languages, $customers, $pages);

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
    	$data = new Webenq_Model_DbTable_Data("data_" . $this->_id);
    	$questions = new Webenq_Model_DbTable_Questions("questions_" . $this->_id);
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	$repDef = $reportDefinitions->find($this->_request->getParam('report-definition-id'))->current();
    	
    	/* get enum options */
    	$outputFormats = $reportDefinitions->getEnumValues('output_format');
    	$reportTypes = $reportDefinitions->getEnumValues('report_type');
    	$languages = $reportDefinitions->getEnumValues('language');
    	$customers = $reportDefinitions->getEnumValues('customer');
    	$pages = $reportDefinitions->getEnumValues('page');
    	
    	/* get form */
    	$form = new Webenq_Form_ReportDefinition($questions->getQuestions(), $outputFormats, $reportTypes, $languages, $customers, $pages);
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
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	
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
    		$post["output_filename"] = $this->_title . '_' . $post["report_type"];
    	}
    	$post["output_filename"] = preg_replace("#[^A-Za-z0-9_-]#", "_", $post["output_filename"] );
    	
    	/* create list of ignore-questions */
    	if (isset($post['ignore_question_ids'])) {    	
	    	$cdlIgnoreQuestionIds = json_encode($post['ignore_question_ids']);
	    	$cdlIgnoreQuestionIds = substr($cdlIgnoreQuestionIds, 1);
	    	$cdlIgnoreQuestionIds = substr($cdlIgnoreQuestionIds, 0, -1);
    	} else {
    		$cdlIgnoreQuestionIds = '';
    	}
    	
    	/* insert report definition */
    	if ($repDefId = $this->_request->getParam('report-definition-id')) {
	    	$reportDefinitions->update(
		    	array(
		    		"data_set_id"			=> $this->_id,
		    		"group_question_id"		=> $post["group_question_id"],
		    		"split_question_id"		=> $post["split_question_id"],
		    		"output_filename"		=> $post["output_filename"],
		    		"output_format"			=> $post["output_format"],
		    		"report_type"			=> $post["report_type"],
		    		"ignore_question_ids"	=> $cdlIgnoreQuestionIds,
		    		"language"				=> $post["language"],
		    		"customer"				=> $post["customer"],
		    		"page"					=> $post["page"],
		    	),
		    	"id = '" . $repDefId . "'");
    	} else {
	    	$reportDefinitions->insert(
		    	array(
		    		"data_set_id"			=> $this->_id,
		    		"group_question_id"		=> $post["group_question_id"],
		    		"split_question_id"		=> $post["split_question_id"],
		    		"output_filename"		=> $post["output_filename"],
		    		"output_format"			=> $post["output_format"],
		    		"report_type"			=> $post["report_type"],
		    		"ignore_question_ids"	=> $cdlIgnoreQuestionIds,
		    		"language"				=> $post["language"],
		    		"customer"				=> $post["customer"],
		    		"page"					=> $post["page"],
		    	)
		    );
    	}
    }


    protected function _processDel()
    {
    	$reportDefinitions = new Webenq_Model_DbTable_ReportDefinitions();
    	$reportDefinitions->delete("id = " . $this->getRequest()->getParam("report-definition-id"));
    }
}