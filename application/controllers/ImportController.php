<?php

class ImportController extends Zend_Controller_Action
{
	/**
	 * Initialisation
	 * 
	 * @return unknown_type
	 */
    public function init()
    {
    	// file to read the data from
//    	$this->filename = "data.csv";
    	$this->filename = "data.ods";
    }
    
    
    /**
     * Index action
     */
    public function indexAction()
    {
    	$this->view->methods = get_class_methods($this);
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
    	$file = new HVA_Model_Input_File_Ods($this->filename);
    	$file->storeData();
    	
    	/* redirect */
//    	$this->_redirect('export/odf');
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
    	$file = new HVA_Model_Input_File_Csv($this->filename);
    	$file->storeData();
    	
    	/* redirect */
//    	$this->_redirect('export/odf');
    }
}