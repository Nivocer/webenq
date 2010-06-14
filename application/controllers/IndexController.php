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
    		$this->view->imports = $imports->fetchAll(
    			$imports->select()->order('date DESC')
    		);
    	} catch (Exception $e) {
    		/* 42S02 = table doesnt exist */
    		if ($e->getCode() !== "42S02") {
    			throw $e;
    		}
    	}
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
	    		$this->_redirect("/");
    		}
    	}
    	
    	$this->view->form = $form;    	
    }


    protected function _processDel()
    {
    	/* get data-set id*/
    	(int) $id = $this->getRequest()->getParam('id');
    	
    	/* get db connection */
    	$db = Zend_Db_Table::getDefaultAdapter()->getConnection();
    	$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    	
    	/* tables to drop */    	
    	$tables = array(
    		"data_$id", "groups_$id", "info_$id",
    		"meta_$id", "questions_$id", "values_$id",
    	);
    	
    	foreach ($tables as $table) {
    		$sql = "DROP TABLE IF EXISTS $table";
    		$db->query($sql);    		
    	}
    	
    	$imports = new HVA_Model_DbTable_Imports();
    	$imports->delete("id = $id");
    	
    	$reportDefinitions = new HVA_Model_DbTable_ReportDefinitions();
    	$reportDefinitions->delete("data_set_id = $id");
    }
}