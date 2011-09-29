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
        $manager->setAttribute(Doctrine_Core::ATTR_USE_NATIVE_ENUM, true);

        $config = $this->getOption('doctrine');
        Doctrine_Core::loadModels($config['models_path'] . '/generated', null, 'Webenq_Model_Base');
        Doctrine_Core::loadModels($config['models_path'], null, 'Webenq_Model');

        if (isset($config['dsn'])) {
            // connect by data source name
            $dsn = $config['dsn'];
        } else {
            // connect by database parameters
            $config = $this->getOption('resources');
            $db = $config['db']['params'];
            $dsn = 'mysql://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . ':' . $db['port'] .
                '/' .  $db['dbname'];
        }
        Doctrine_Manager::connection($dsn, 'doctrine');

        return $manager;
    }

    protected function _initDatabaseSchemaVersion()
    {
        $config = $this->getOption('doctrine');
        $migration = new Doctrine_Migration($config['migrations_path']);
        $current = (int) $migration->getCurrentVersion();
        $latest = (int) $migration->getLatestVersion();
        if ($current !== $latest) {
            throw new Exception('Database schema out of date! Current version is '
                . $migration->getCurrentVersion() . ', latest version is '
                . $migration->getLatestVersion());
        }
    }

    protected function _initI18n()
    {
        $translate = new Zend_Translate(array(
            'adapter' => 'array',
            'content' => APPLICATION_PATH . '/translations/en/',
            'locale'  => 'en',
        ));
        $translate->addTranslation(array(
            'content' => APPLICATION_PATH . '/translations/nl/',
            'locale'  => 'nl',
        ));
        Zend_Registry::set('Zend_Translate', $translate);

        /**
         * global function that can be used in templates to translate strings
         */
        function t($string)
        {
            $translate = Zend_Registry::get('Zend_Translate');
            $locale = Zend_Registry::get('Zend_Locale');
            $translate->setLocale($locale);
            return $translate->translate($string, $locale);
        }
    }
}