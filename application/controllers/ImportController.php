<?php

class ImportController extends Zend_Controller_Action
{
	/**
	 * Name of the file to process
	 */
	protected $_filename;
	
	
	/**
	 * Supported input formats
	 */
	protected $_supportedFormats = array();
	
	
	/**
	 * Initialisation
	 * 
	 * @return void
	 */
    public function init()
    {
    	/* start session and get session id */
    	$this->_session = new Zend_Session_Namespace("webenq");
    	$this->_sessionId = Zend_Session::getId();
    	
    	/* get supported import formats */    	
    	$this->_supportedFormats = $this->_getSupportedFormats();    	
    }
    
    
    /**
     * Gets all supported import formats (based on defined controller actions)
     * 
     * @return array
     */
    protected function _getSupportedFormats()
    {
    	$methods = get_class_methods($this);
    	
    	foreach ($methods as $i => $method) {
    		if (substr($method, -6) === 'Action' && $method !== 'indexAction') {
				$methods[$i] = substr($method, 0, -6);
    		} else {
    			unset($methods[$i]);
    		}
    	}
    	
    	return $methods;
    }
    
    
    /**
     * Index action
     */
    public function indexAction()
    {
    	$form = new HVA_Form_Import($this->_supportedFormats);
    	$errors = array();
    	
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
    			if (!$form->file->receive()) {
    				$errors[] = 'Error receiving the file';
    			} else {
    				$this->_filename = $form->file->getFileName();
    				$extension = array_pop(split('\.', $this->_filename));
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
    	$file->storeData();
    }
    
    
    /**
     * Imports CVS file and builds db table based on headers
     * 
     * @return void
     */
    public function csvAction()
    {
    	/* disable view renderer */
    	$this->_helper->viewRenderer->setNoRender();
    	
    	/* open file, store data, and close file */
    	$file = new HVA_Model_Input_File_Csv($this->_filename);
    	$file->storeData();
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