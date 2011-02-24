<?php

class TestController extends Zend_Controller_Action
{
	/**
	 * Name of the file to process
	 */
	protected $_filename;
	
    /**
     * Index action
     */
    public function indexAction()
    {
    	$form = new Webenq_Form_Test_Index();
    	$errors = array();
    	$question = null;
    	
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
    			
    			$form->file->receive();
    			
    			if (!$form->file->isReceived()) {
    				$errors[] = 'Error receiving the file';
    			}
    			
    			if (!$errors) {
    				try {
    					$filename = $form->file->getFileName();
    					$question = $this->_processTest($filename);
    				} catch (Exception $e) {
    					$errors[] = 'Error processing the file: ' . $e->getMessage();
    				}
    			}    			
    		}
    	}
    	
    	$this->view->form = $form;
    	$this->view->errors = $errors;    	
    	$this->view->question = $question;    	
    }
    
    
    /**
     * Processes the testfile
     * 
     * @return void
     */
    protected function _processTest($file)
    {
    	$f = fopen($file, 'r');
    	
    	$data = array();
    	while ($line = fgets($f)) {
    		$data[] = trim($line);
    	}
    	
    	return Webenq_Model_Data_Question::factory($data);
    }
}