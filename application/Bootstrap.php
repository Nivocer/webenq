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

        $config = $this->getOption('db');
        if (isset($config['dsn'])) {
            // connect by data source name
            $dsn = $config['dsn'];
        } else {
            // connect by database parameters
            $dsn = 'mysql://' . $config['username'] . ':' . $config['password']
                . '@' . $config['host'] . ':' . $config['port'] .
                '/' .  $config['dbname'];
        }
        Doctrine_Manager::connection($dsn, 'doctrine');

        return $manager;
    }

    protected function _initSession()
    {
        /**
         * We're using a custom savehandler that saves session data to a MySQL database
         * (using Doctrine) to prevent configuration-related problems on the server
         * (such as permissions, openbasedir).
         */
        if (version_compare(phpversion(), '5.3', '>=')) {
            Zend_Session::setSaveHandler(new Webenq_Session_SaveHandler());
        }
        try {
            Zend_Session::start();
        } catch (Zend_Session_Exception $e) {
//            echo $e->getMessage();
        }
    }

    protected function _initI18n()
    {
        $languages = array();

        // get preferred languages from $_SERVER variable
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $parts);

            if (count($parts[1]) > 0) {
                // create a list like "en" => 0.8
                $languages = array_combine($parts[1], $parts[4]);

                // set default to 1 for any without q factor
                foreach ($languages as $language => $value) {
                    if ($value === '') $languages[$language] = 1;
                }

                // sort list based on value
                arsort($languages, SORT_NUMERIC);

                // build array with languages as values
                $languages = array_keys($languages);

                // remove countries ("en-US" becomes "en")
                foreach ($languages as $key => $language) {
                    if (strlen($language) > 2) {
                        $languages[$key] = substr($language, 0, 2);
                    }
                }

                // remove double values and reset keys
                $languages = array_merge(array_unique($languages), array());
            }
        }

        // add preferred languages from config file
        $languages = array_merge($languages, $this->getOption('preferredLanguages'));

        // remove double values and reset keys
        $languages = array_merge(array_unique($languages), array());

        // store array with preferred languages to registry
        Zend_Registry::set('preferredLanguages', $languages);

        // init translations
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

        if (!function_exists('t')) {
            /**
             * Global function that can be used in templates to translate strings
             *
             * @param string $string String to translate
             * @return string Translated string
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
}