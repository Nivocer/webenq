<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Bootstrap init
     */
//    protected function _initBootstrap()
//    {
//        $this->bootstrap('Db');
//   		$this->bootstrap('FrontController');
//    }
	
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

    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('Webenq Modules');
        $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
}