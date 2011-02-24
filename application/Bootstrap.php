<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
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
}