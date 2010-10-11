<?php

class ImportController extends Zend_Controller_Action
{
	/**
	 * Name of the file to process
	 */
	protected $_filename;
	
	/**
	 * Supported input formats
	 * 
	 * For every entry there must be a corresponding action in this controller,
	 * so if 'ods' is in the list, this controller must have an action 'odsAction'.
	 */
	protected $_supportedFormats = array('ods', 'xls');
	
    /**
     * Index action
     */
    public function indexAction()
    {
    	$form = new HVA_Form_Import($this->_supportedFormats);
    	$errors = array();
    	
    	if ($this->_request->isPost()) {
    		if ($form->isValid($this->_request->getPost())) {
    			if (!$form->file->receive()) {
    				$errors[] = 'Error receiving the file';
    			} else {
    				$this->_filename = $form->file->getFileName();
    				$filenameParts = preg_split('#\.#', $this->_filename);
    				$extension = array_pop($filenameParts);
    			}
    			if (!$errors) {
    				try {
    					$action = $extension . 'Action';
    					$this->{$action}();
    				} catch (Exception $e) {
    					$errors[] = 'Error processing the files';
    				}

    				$this->_redirect('index');
    			}    			
    		}
    	}
    	
    	$this->view->errors = $errors;    	
    	$this->view->form = $form;
    }
    
    /**
     * Imports ODS file and builds db table based on headers
     * 
     * @return void
     */
    public function odsAction()
    {
    	/* disable view renderer */
    	$this->_helper->viewRenderer->setNoRender();
    	
    	/* open file, store data, and close file */
    	$file = new HVA_Model_Input_File_Ods($this->_filename);
    	$file->store();
    }
    
    /**
     * Imports XLS file and builds db table based on headers
     * 
     * @return void
     */
    public function xlsAction()
    {
    	/* disable view renderer */
    	$this->_helper->viewRenderer->setNoRender();
    	
    	/* open file, store data, and close file */
    	$file = new HVA_Model_Input_File_Xls($this->_filename);
    	$file->store();
    }
    
    /**
     * Returns the supported input formats
     * 
     * @return array Supported input formats
     */    
    public function getSupportedFormats()
    {
    	return $this->_supportedFormats;
    }
    
    /**
     * Sets the data file
     * 
     * @param string $filename Name of datafile
     */
    public function setDataFile($filename)
    {
    	$this->_filename = $filename;
    }
}