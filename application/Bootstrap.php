<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Bootstrap init
     */
    protected function _initBootstrap()
    {
        $this->bootstrap('Db');
   		$this->bootstrap('FrontController');
    }
	
    /**
     * Bootstrap autoloader for application resources
     * 
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload()
    {
   		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'HVA',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }
}