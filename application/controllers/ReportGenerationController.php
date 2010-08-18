<?php

class ReportGenerationController extends Zend_Controller_Action
{
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
    	$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
    	$host	= $config->resources->db->params->host;
    	$port	= $config->resources->db->params->port;
    	$db		= $config->resources->db->params->dbname;
    	$user	= $config->resources->db->params->username;
    	$pass	= $config->resources->db->params->password;
    	
    	/* remove old report */
    	$repDef = new HVA_Model_DbTable_ReportDefinitions();
    	$row = $repDef->find($this->_id)->current();
    	$file = APPLICATION_PATH . "/../public/reports/" . $row->output_filename . "." . $row->output_format;
    	if (file_exists($file)) {
    		unlink($file);
    	}
    	
    	/* prepare */
    	if ($row->report_type === 'barcharts') {
    		$this->_generateBarcharts($row);
    	}
    	
    	/* init vars */
    	$cwd = getcwd();
    	$output = array();
    	$returnVar = 0;
    	
    	/* create new report */
    	chdir(APPLICATION_PATH . "/../java");
    	$cmd = "java -cp .:./lib/bisiLibJasper.jar:./lib/bisiResources.jar:./lib/mysql-connector-java-5.1.6-bin.jar:./lib/poi-3.5-FINAL-20090928.jar:./lib/jasperreports-3.7.2.jar:./lib/iText-2.1.7.jar:./lib/commons-logging-1.1.1.jar:./lib/commons-digester-2.0.jar:./lib/commons-collections-3.2.1.jar:./lib/commons-beanutils-1.8.3.jar it.bisi.report.jasper.ExecuteReport $host:$port/$db $user $pass $this->_id";
		ob_start();
    	passthru($cmd, $returnVar);
		$output = ob_get_contents();
		ob_end_clean();
    	chdir($cwd);
    	
    	/* error output? */
    	if ($returnVar > 0) {
    		$this->view->output = $output;
    		return;
    	}
    	
    	/* has file (or multiple files) been created? */
    	$file = $row->output_filename . "." . $row->output_format;
    	if (file_exists('reports/' . $file)) {
	    	$fileInfo = stat('reports/' . $file);
	    	$timeDiff = $fileInfo['mtime'] - time();
	    	if ($timeDiff < 2) {
		    	$this->view->file = $row->output_filename . "." . $row->output_format;
		    	return;
	    	}
    	} else {
    		$files = scandir('reports/');
    		$reports = array();
    		foreach ($files as $f) {
    			$fileName = substr($f, 0, strlen($row->output_filename));
    			$fileExt = substr($f, -1 * strlen($row->output_format));
    			if ($fileName === $row->output_filename && $fileExt === $row->output_format) {
    				$reports[] = $f; 
    			}
    		}
    		if (count($reports) > 0) {
	    		$this->view->file = $reports;
	    		return;
    		}
    	}
    	
   		$this->view->output = "Onbekende fout opgetreden bij het genereren van het rapport.";
    }
    
    protected function _generateBarcharts($row)
    {
    	/* make directory */
    	if (!is_dir("reports/images")) {
    		mkdir("reports/images");
    		system("chmod 664 reports/images");
    	}
    	
    	/* get questions */
    	$questionsModel = new HVA_Model_DbTable_Questions("questions_" . $row->data_set_id);
    	$questions = $questionsModel->fetchAll("group_id > 0");
    	$splitBy = $row->split_question_id;
    	
    	/* get answers */
    	foreach ($questions as $question) {
    		$answers = $questionsModel->getAnswers($question->id, $splitBy);
    		if ($answers instanceof HVA_Model_Data_Question_Closed_Scale) {
    			$filename = "reports/images/bar_report_" . $row->id . "_question_" . $question->id . ".png";
    			$answers->generateBarchart($filename);
    			system("chmod 664 " . $filename);
    		} elseif (is_array($answers)) {
    			foreach ($answers as $answer => $part) {
	    			$filename = "reports/images/bar_report_" . $row->id . "_question_" . $question->id . "_splitquestion_" . $splitBy . "_splitanswer_" . $answer . ".png";
	    			$part->generateBarchart($filename);
	    			system("chmod 664 " . $filename);
    			}
    		}
    	}
    }
}