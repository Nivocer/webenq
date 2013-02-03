<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller plugin for setting up ACL
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
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

        $frontController = Zend_Controller_Front::getInstance();

        $canAccessInstaller = isset($config['canAccessInstaller']) ?
            (bool) $config['canAccessInstaller'] : false;

        // check if installer is requested
        if ($request->getModuleName() === 'default' && $request->getControllerName() === 'install') {
            if ($canAccessInstaller) {
                foreach ($frontController->getPlugins() as $plugin) {
                    $frontController->unregisterPlugin($plugin);
                }
                return;
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