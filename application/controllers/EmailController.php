<?php

class EmailController extends Zend_Controller_Action
{
	protected $_email;
	
	protected $_filePatterns = array(
		'fraijlemaborg' => '#^fraijlemaborg-open-docent-#',
	);
	
	public function init()
	{
		$this->_email = new HVA_Model_DbTable_Email();
	}
	
    public function scanAction()
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
    	
    	/* remove old add reports from db */
    	foreach ($this->_email->fetchAll() as $report) {
    		if (!in_array($report->filename, $files)) {
    			$this->_email->delete("filename = '" . $report->filename . "'");
    		}
    	}
    	
    	$this->_redirect('/email');
    }
    
    public function indexAction()
    {
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
    
    public function addAction()
    {
    	$form = $this->view->form = new HVA_Form_Email_Add(array('csv'));
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
    					$this->{$action}();
    				} catch (Exception $e) {
    					$errors[] = 'Error processing the files';
    				}
    				
    				if (!$errors) {
    					$this->_redirect('email');
    				}
    			}    			
    		}
    	}
    }
    
    protected function _processCsv()
    {
    	$delimiter = ',';
    	$enclosure = '"';
    	
    	/* open file */
    	$fp = fopen($this->_filename, "r");
    	
    	/* read line from file */
    	while ($data = fgetcsv($fp, 0, $delimiter, $enclosure)) {
    		/* fetch matching row from db */
    		$rowset = $this->_email->fetchAll("filename LIKE '%" . trim($data[0]) . ".%'");
    		if ($rowset instanceof Zend_Db_Table_Rowset) {
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
    	
    	/* close file */
    	fclose($fp);
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
	    		$this->_redirect("/email/scan");
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