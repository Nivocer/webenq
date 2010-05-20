<?php

class IndexController extends Zend_Controller_Action
{
	/**
	 * Initialisation
	 * 
	 * @return void
	 */
    public function init()
    {
    }
	
	/**
     * Renders the dashboard
     */
    public function indexAction()
    {
    	$imports = new HVA_Model_DbTable_Imports();
    	try {
    		$this->view->imports = $imports->fetchAll();
    	} catch (Exception $e) {}
    }
}