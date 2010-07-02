<?php

class EmailController extends Zend_Controller_Action
{
	protected $_email;
	
	protected $_filePatterns = array(
		'fraijlemaborg' => '#^fraijlemaborg-open-(docent|opleiding)-#',
	);
	
	public function init()
	{
		$this->_email = new HVA_Model_DbTable_Email();
	}
	
    protected function _scan()
    {
    	$files = scandir('reports');
    	$foundReports = array();
    	
    	foreach ($this->_filePatterns as $filePatternName => $filePattern) {
	    	foreach ($files as $file) {
	    		if (preg_match($filePattern, $file)) {
	    			$foundReports[$filePatternName][] = $file;
	    		}
	    	}
    	}
    	
    	/* add new reports to db */
    	foreach ($foundReports as $customer => $reports) {
    		foreach ($reports as $report) {
    			try {
    				$this->_email->insert(array(
    					'filename'	=> $report,
    					'customer'	=> $customer,
    				));
    			} catch (Exception $e) {
    				if ($e->getCode() !== "23000") {
    					throw $e;
    				}
    			}
    		}
    	}
    	
    	/* remove old reports from db */
    	foreach ($this->_email->fetchAll() as $report) {
    		if (!in_array($report->filename, $files)) {
    			$this->_email->delete("filename = '" . $report->filename . "'");
    		}
    	}
    }
    
    public function indexAction()
    {
    	$this->_scan();
    	
    	try {
	    	$this->view->reports = $this->_email->fetchAll($this->_email->select()
	    		->order(array("customer", "filename"))
	    	);
    	} catch (Exception $e) {
    		if ($e->getCode() === "42S02") {
    			$this->_email->createTable();    			
    		} else {
    			throw $e;
    		}
    	}
    }
    
    public function mergeAction()
    {
    	$form = $this->view->form = new HVA_Form_Email_Merge(array('csv'));
    	$errors = array();
    	
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
    			if (!$form->file->receive()) {
    				$errors[] = 'Error receiving the file';
    			} else {
    				$this->_filename = $form->file->getFileName();
    				$filenameParts = preg_split('#\.#', $this->_filename);
    				$extension = array_pop($filenameParts);
    			}
    			if (!$errors) {
    				try {
    					$action = "_process" . ucfirst($extension);
    					$this->{$action}('firstTeacher');
    				} catch (Exception $e) {
    					throw $e;
    					$errors[] = 'Error processing the files';
    				}
    				
    				if (!$errors) {
    					$this->_redirect('email');
    				} else {
    					$this->view->errors = $errors;
    				}
    			}    			
    		}
    	}
    }
    
    public function mergeExtraAction()
    {
    	$form = $this->view->form = new HVA_Form_Email_Merge(array('csv'));
    	$form->file->setLabel('Selecteer het bestand met email-adressen van dubbele docenten:');
    	$errors = array();
    	
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
    			if (!$form->file->receive()) {
    				$errors[] = 'Error receiving the file';
    			} else {
    				$this->_filename = $form->file->getFileName();
    				$filenameParts = preg_split('#\.#', $this->_filename);
    				$extension = array_pop($filenameParts);
    			}
    			if (!$errors) {
    				try {
    					$action = "_process" . ucfirst($extension);
    					$this->{$action}('extraTeachers');
    				} catch (Exception $e) {
    					throw $e;
    					$errors[] = 'Error processing the files';
    				}
    				
    				if (!$errors) {
    					$this->_redirect('email');
    				} else {
    					$this->view->errors = $errors;
    				}
    			}    			
    		}
    	}
    }
    
    protected function _processCsv($mode=null)
    {
    	$delimiter = ',';
    	$enclosure = '"';
    	
    	/* open file */
    	$fp = fopen($this->_filename, "r");
    	
    	/* ignore first line (headers) */
    	fgetcsv($fp, 0, $delimiter, $enclosure);
    	
    	/* read line from file */
    	while ($data = fgetcsv($fp, 0, $delimiter, $enclosure)) {
    		
	    	/* search for relevant files */
    		$files = scandir('reports');
	    	$foundReports = array();
	    	
	    	/* set pattern depending on mode */
	    	switch ($mode) {
	    		case 'firstTeacher':
	    			$filePattern = "#^fraijlemaborg_open_.*-" . $data[0] . "\.pdf$#";
	    			$output = "fraijlemaborg-open-docent-";
	    			break;
	    		case 'extraTeachers':
	    			$filePattern = "#^fraijlemaborg_open_.*-" . $data[2] . "-" . $data[1] . "-.*\.pdf$#";
	    			$output = "fraijlemaborg-open-docent-extra-";
	    			break;
	    		default:
	    			throw new Exception("Invalid mode!");
	    	}
	    	
	    	foreach ($files as $file) {
	    		if (preg_match($filePattern, $file)) {
	    			$foundReports[] = $file;
	    		}
	    	}
	    	
	    	/* merge files */
	    	if (count($foundReports) > 0) {
	    		$cmd = "pdftk ";
	    		foreach ($foundReports as $report) {
	    			$cmd .= "reports/" . $report . " ";
	    		}
	    		$cmd .= "cat output reports/" . $output . $data[0] . ".pdf";
	    		system($cmd);
	    	}
	    	
	    	if ($mode === 'firstTeacher') {
	    		/* fetch matching row from db */
	    		$rowset = $this->_email->fetchAll("filename LIKE '%" . trim($data[0]) . ".%'");
	    		if ($rowset instanceof Zend_Db_Table_Rowset && $rowset->count() > 0) {
	    			/* loop through rows */
	    			foreach ($rowset as $row) {
	    				/* any changes? update and reset "sent" */
		    			if ($row->teacher !== trim($data[0]) || $row->email !== trim($data[1])) {
			    			$this->_email->update(
			    				array(
			    					"teacher"	=> trim($data[0]),
			    					"email"		=> trim($data[1]),
			    					"sent"		=> 0, 
			    				),
			    				"id = $row->id"
			    			);
		    			}
	    			}
	    		}
	    	}
    	}
    		
    	/* close file */
    	fclose($fp);
    	
    	$this->_redirect("email");
    }
    
    public function mergeCourseAction()
    {
    	$courses = array();
    	$foundReports = array();
    	$output = "fraijlemaborg-open-opleiding-";
    	
    	/* get all files */
    	$files = scandir('reports');
    	
    	/* get courses from file names */
    	$filePattern = "#^fraijlemaborg_open_(.*)-.*-.*-.*\.pdf$#";
    	foreach ($files as $file) {
    		if (preg_match($filePattern, $file, $matches)) {
    			$courses[] = $matches[1];
    		}
    	}
    	$courses = array_unique($courses);
    	
    	/* get reports for courses */
    	foreach ($courses as $course) {
    		$filePattern = "#^fraijlemaborg_open_" . $course . "-.*-.*-.*\.pdf$#";
    		foreach ($files as $file) {
	    		if (preg_match($filePattern, $file)) {
	    			$foundReports[$course][] = $file;
	    		}
	    	}
    	}
    	
    	/* merge reports */
    	if (count($foundReports) > 0) {
    		foreach ($foundReports as $course => $reports) {
	    		$cmd = "pdftk ";
	    		foreach ($reports as $report) {
	    			$cmd .= "reports/" . $report . " ";
	    		}
	    		$cmd .= "cat output reports/" . $output . $course . ".pdf";
	    		system($cmd);
    		}
    	}
    		
    	$this->_redirect("email");
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
	    		$this->_redirect("email");
    		}
    	}
    	
    	$this->view->form = $form;    	
    }


    protected function _processDel()
    {
    	/* get report by id*/
    	(int) $id = $this->getRequest()->getParam('id');
    	$report = $this->_email->fetchRow("id = $id");
    	
    	/* remove file */
    	$file = getcwd() . '/reports/' . $report->filename;
    	if (file_exists($file)) {
    		unlink($file);
    	}
    }
    
    
    public function sendAction()
    {
    	/* get report by id */
    	(int) $id = $this->_request->getParam('id');
    	$report = $this->view->report = $this->_email->fetchRow("id = $id");
    	
    	/* get mail config options */
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    	$test = $config->{APPLICATION_ENV}->email->test;
    	$smtp = $config->{APPLICATION_ENV}->email->smtp;
    	
    	/* set mail transport */
    	$transport = new Zend_Mail_Transport_Smtp($smtp->host, array(
    		'ssl'		=> $smtp->ssl,
    		'port'		=> $smtp->port,
    		'auth'		=> $smtp->auth,
    		'username'	=> $smtp->username,
    		'password'	=> $smtp->password,
    	));
    	Zend_Mail::setDefaultTransport($transport);
    	
    	/* build messages */
    	$messageText = $this->view->render('email/' . $report->customer . '/message-text.phtml');
    	$messageHtml = $this->view->render('email/' . $report->customer . '/message-html.phtml');
    	
    	/* build attachment */
    	$filename = getcwd() . '/reports/' . $report->filename;
    	$content = file_get_contents($filename);
    	$attachment = new Zend_Mime_Part($content);
    	$attachment->type = "application/pdf";
    	$attachment->encoding = Zend_Mime::ENCODING_BASE64;
    	$attachment->filename = $report->filename;
    	
    	/* instantiate mail object */
    	$mail = new Zend_Mail();
    	$mail->setFrom("pietje@example.com", "pietje@example.com")
    		->setBodyText($messageText)
    		->setBodyHtml($messageHtml)
    		->addAttachment($attachment);
    		
    	/* add test address or real address, and send mail */
    	if ($test->use === "1") {
    		$mail->addTo($test->address, $test->address)->send();
    	} else {
			$mail->addTo($report->email, $report->teacher)->send();
    	}
    	
    	/* update db */
    	$this->_email->update(
    		array("sent" => "1"),
    		"id = $id"
    	);

    	$this->_redirect("email");
    }
}