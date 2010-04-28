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
    	/* start session and get session id */
    	$this->_session = new Zend_Session_Namespace("webenq");
    	$this->_sessionId = Zend_Session::getId();
    }
	
	/**
     * Renders the dashboard
     */
    public function indexAction()
    {
    }
}