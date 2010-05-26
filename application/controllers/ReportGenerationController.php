<?php

class ReportGenerationController extends Zend_Controller_Action
{
	/**
	 * Initialisation
	 * 
	 * @return void
	 */
    public function init()
    {
    	$this->_id = $this->getRequest()->getParam("id");
    	
    	if (!$this->_id) {
    		throw new Exception("No id given!");
    	}
    }
	
    public function indexAction()
    {
    	$config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
    	$host	= $config->resources->db->params->host;
    	$db		= $config->resources->db->params->dbname;
    	$user	= $config->resources->db->params->username;
    	$pass	= $config->resources->db->params->password;
    	
    	chdir(APPLICATION_PATH . "/../java");
    	$cmd = "java -cp .:./lib/bisiLibJasper.jar:./lib/bisiResources.jar:./lib/mysql-connector-java-5.1.6-bin.jar:./lib/poi-3.5-FINAL-20090928.jar:./lib/jasperreports-3.7.2.jar:./lib/iText-2.1.7.jar:./lib/commons-logging-1.1.1.jar:./lib/commons-digester-2.0.jar:./lib/commons-collections-3.2.1.jar:./lib/commons-beanutils-1.8.3.jar it.bisi.report.jasper.ExecuteReport $host/$db $user $pass $this->_id";
    	//system($cmd);
    	die($cmd);
    }
}