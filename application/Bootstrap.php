<?php
/**
 * Application bootstrap class
 *
 * @package     Webenq
 * @subpackage  Bootstrap
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResourceAutoLoading()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH,
            'namespace' => 'Webenq',
        ));
        $loader->addResourceType('doctrine', 'models/generated/Base', 'Model_Base');
        $loader->addResourceType('model', 'models', 'Model');
    }

    protected function _initDoctrine()
    {
        require_once 'Doctrine.php';

        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->pushAutoloader(array('Doctrine', 'autoload'));
        $loader->registerNamespace('sfYaml')
            ->pushAutoloader(array('Doctrine', 'autoload'), 'sfYaml');

        $manager = Doctrine_Manager::getInstance();
//        $manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

//        $config = $this->getOption('doctrine');
//        Doctrine_Core::loadModels($config['models_path'] . '/generated', null, 'Webenq_Model_Base');
//        Doctrine_Core::loadModels($config['models_path'], null, 'Webenq_Model');

        $config = $this->getOption('doctrine');
        if (isset($config['dsn'])) {
            // connect by data source name
            $conn = Doctrine_Manager::connection($config['dsn'], 'doctrine');
        } else {
            // connect by database parameters
            $config = $this->getOption('resources');
            $db = $config['db']['params'];
            $dsn = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] .
                '/' .  $db['dbname'];
            $conn = Doctrine_Manager::connection($dsn, 'doctrine');
        }

        return $manager;
    }
}