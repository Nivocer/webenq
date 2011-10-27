<?php
/**
 * Controller plugin for setting up ACL
 *
 * @package     Webenq
 * @subpackage  Plugins
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Plugin_Schema extends Zend_Controller_Plugin_Abstract
{
    /**
     * Checks if the required db schema is used
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // get installer config settings
        $config = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getOption('installation');

        $canAccessInstaller = isset($config['canAccessInstaller']) ?
            (bool) $config['canAccessInstaller'] : false;

        // check if installer is requested
        if ($request->getModuleName() === 'default' && $request->getControllerName() === 'install') {
            if ($canAccessInstaller) {
                $frontController = Zend_Controller_Front::getInstance();
                foreach ($frontController->getPlugins() as $plugin) {
                    $frontController->unregisterPlugin($plugin);
                }
            } else {
                die('Database schema out of date! Please run the installer.');
            }
        }

        // get doctrine config settings
        $config = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')->getOption('doctrine');

        // get current db schema version
        $migration = new Doctrine_Migration($config['migrations_path']);
        $current = (int) $migration->getCurrentVersion();

        // get required db schema version
        $latest = (int) $migration->getLatestVersion();

        if ($current !== $latest) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit('index', 'install', 'default');
        }
    }
}