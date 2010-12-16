<?php

class IndexController extends Zend_Controller_Action
{
	/**
     * Renders the dashboard
     * 
     * @return void
     */
    public function indexAction()
    {
    	$this->_helper->actionStack('index', 'questionnaire');
    	$this->_helper->actionStack('index', 'import');
    	$this->_helper->viewRenderer->setNoRender();
    }
}
