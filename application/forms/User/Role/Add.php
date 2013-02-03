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
 * Form class
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_User_Role_Add extends Zend_Form
{
    public function init()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->setAction("$baseUrl/user/role");

        $this->addElements(
            array(
                $this->createElement(
                    'text',
                    'name',
                    array(
                        'label' => 'new role',
                        'required' => true,
                        'filters' => array('StringToLower'),
                        'validators' => array('Alpha'),
                    )
                ),
                $this->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'add',
                    )
                ),
            )
        );
    }

    public function store()
    {
        try {
            $role = new Webenq_Model_Role();
            $role->name = $this->name->getValue();
            $role->save();
            return true;
        }
        catch (Doctrine_Connection_Mysql_Exception $e) {
            if ($e->getCode() == 23000) {
                $this->name->addError('This name is already in use for a different role');
            } else {
                $this->name->addError('Unknown error occured');
            }
            return false;
        }
    }
}