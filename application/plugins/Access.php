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
class Webenq_Plugin_Access extends Zend_Controller_Plugin_Abstract
{
    /**
     * This role must be defined in the database and will be assigned to
     * all anonymous users.
     *
     * @var string
     */
    static protected $_anonymousRole = 'visitor';

    /**
     * This route is accessible for all and is used to redirect to when
     * access is denied to the requested resource. This could be the
     * login page for instance.
     *
     * @var array Array with key-value pairs for controller and action
     */
    static protected $_defaultRoute = array(
            'controller' => 'user',
            'action' => 'login',
    );

    protected $_requestingRole;

    /**
     * Sets up the ACL system
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     * @throws Zend_Acl_Exception when user has no access
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $acl = $this->_setupAcl();
        $auth = Zend_Auth::getInstance();

        /* requesting role */
        if (!$this->_requestingRole) {
            if ($auth->hasIdentity()) {
                $user = Doctrine_Core::getTable('Webenq_Model_User')->find($auth->getIdentity()->id);

                if (!$user) {
                    $auth->clearIdentity();
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                    $redirector->gotoUrlAndExit($request->getBaseUrl());
                }
                $this->_requestingRole = $user->Role->name;
            } elseif ($request->getParam('api_key')) {
                //api-key access
                $user = Doctrine_Core::getTable('Webenq_Model_User')->findOneBy(
                    'api_key', $request->getParam('api_key')
                );
                $this->_requestingRole = $user->Role->name;
                if (!$user) {
                    $auth->clearIdentity();
                    // send response header: no valid key?
                }
            } else {
                //
                $this->_requestingRole = self::$_anonymousRole;
            }
        }

        /* requested resource */
        $requestedResource = $request->getControllerName() . '/' . $request->getActionName();

        /* check access */
        try {

            /* always allow access to default route */
            $defaultResource = self::$_defaultRoute['controller'] . '/' . self::$_defaultRoute['action'];
            if ($requestedResource === $defaultResource) {
                return;
            }

            /* throw exception if no access */
            if (!$acl->isAllowed($this->_requestingRole, $requestedResource)) {
                throw new Zend_Acl_Exception(
                    "Role '$this->_requestingRole' has no access to requested resource '" .
                    $requestedResource . "'"
                );
            }

        } catch (Zend_Acl_Exception $e) {

            /* catch any thrown acl-exception and redirect to default route */
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit(
                self::$_defaultRoute['action'],
                self::$_defaultRoute['controller'], null, array(
                    'redirect' => base64_encode($request->getPathInfo()),
                )
            );
        }
    }

    static public function getAnonymousRole()
    {
        return self::$_anonymousRole;
    }

    protected function _setupAcl()
    {
        if (!Zend_Registry::isRegistered('acl')) {
            $roles = Doctrine_Core::getTable('Webenq_Model_Role')->findAll();
            $resources = Doctrine_Core::getTable('Webenq_Model_Resource')->findAll();
            $rolesResources = Doctrine_Core::getTable('Webenq_Model_RoleResource')->findAll();
            $acl = new Zend_Acl();

            /* add roles */
            foreach ($roles as $role) {
                $acl->addRole($role->name);
            }

            try {
                $acl->getRole(self::$_anonymousRole);
            } catch (Zend_Acl_Role_Registry_Exception $e) {
                throw new Exception('The role \'' . self::$_anonymousRole . '\' must be defined!');
            }

            /* add resources */
            foreach ($resources as $resource) {
                $acl->add(new Zend_Acl_Resource($resource->name));
            }

            /* add permissions */
            foreach ($rolesResources as $roleResource) {
                $acl->allow($roleResource->Role->name, $roleResource->Resource->name);
            }
            /* register ACL */
            Zend_Registry::set('acl', $acl);
        }

        return Zend_Registry::get('acl');
    }
}
