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
	 * @return unknown_type
	 */
    public function init()
    {
    	/* get supported input formats (based on existing actions) */
    	$methods = get_class_methods($this);
    	
    	foreach ($methods as $i => $method) {
    		if (substr($method, -6) === 'Action' && $method !== 'indexAction') {
				$methods[$i] = substr($method, 0, -6);
    		} else {
    			unset($methods[$i]);
    		}
    	}
    	$this->_supportedFormats = $methods;
    }
    
    
    /**
     * Index action
     */
    public function indexAction()
    {
    	$form = new HVA_Form_Import($this->_supportedFormats);
    	
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
    			if (!$form->file->receive()) {
    				$form->addError('Error receiving the file');
    			} else {
    				$this->_filename = $form->file->getFileName();
    				$extension = array_pop(split('\.', $this->_filename));
    				$this->{$extension . 'Action'}();
    				$this->_redirect('export');
    			}    			
    		}
    	}
    	
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