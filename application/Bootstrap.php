<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
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

	protected function _initDoctrine()
	{
        require_once 'Doctrine.php';
        
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->pushAutoloader(array('Doctrine', 'autoload'));
        
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models/doctrine/generated');
        Doctrine_Core::loadModels(APPLICATION_PATH . '/models/doctrine');
        
        $config = $this->getOption('resources');
		$db = $config['db']['params'];
		$url = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] . '/' .  $db['dbname'];
        $conn = Doctrine_Manager::connection($url, 'doctrine');
        
        return $manager;
	}
	
	protected function _initDefineConstants()
	{
		define('COLLECTION_PRESENTATION_OPEN_TEXT', 'open_text');
		define('COLLECTION_PRESENTATION_OPEN_TEXTAREA', 'open_textarea');
		define('COLLECTION_PRESENTATION_OPEN_DATE', 'open_date');
		define('COLLECTION_PRESENTATION_OPEN_CURRENTDATE', 'open_date_current');
		define('COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST', 'singleselect_dropdownlist');
		define('COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS', 'singleselect_radiobuttons');
		define('COLLECTION_PRESENTATION_MULTIPLESELECT_LIST', 'multipleselect_list');
		define('COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES', 'multipleselect_checkboxes');
	}
}