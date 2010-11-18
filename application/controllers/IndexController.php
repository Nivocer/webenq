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
     * 
     * @return void
     */
    public function indexAction()
    {
    	$this->view->questionnaires =
    		Doctrine_Core::getTable('Questionnaire')->findAll();
    }


    /**
     * Renders the confirmation form for deleting a questionnaire
     * 
     * @return void
     */
    public function delAction()
    {
    	/* get form */
    	$form = new Zend_Form();
    	$confirm = new Zend_Form_Element_Submit('confirm');
    	$confirm->setLabel("ja, verwijderen")->setValue("yes");
    	$form->addElement($confirm);

    	/* process form */
    	if ($this->getRequest()->isPost()) {
    		if ($form->isValid($this->getRequest()->getPost())) {
	    		$this->_processDel();
	    		$this->_redirect("/");
    		}
    	}
    	
    	/* display form */
    	$this->view->form = $form;    	
    }


    /**
     * Remove a questionnaire
     * 
     * @return void
     */
    protected function _processDel()
    {
    	/* get questionnaire id*/
    	(int) $id = $this->_request->id;
    	
    	/* get and delete questionnaire */
    	$questionnaire = Doctrine_Core::getTable('Questionnaire')->find($id);
    	$questionnaire->delete();
    }
}