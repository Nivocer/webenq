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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_User_Role_Edit extends Webenq_Form_User_Role_Add
{
    protected $_role;

    public function __construct(Webenq_Model_Role $role, $options = null)
    {
        $this->_role = $role;
        parent::__construct($options);
    }

    public function init()
    {
        parent::init();

        $this->addElement(
            $this->createElement(
                'hidden',
                'id',
                array(
                    'value' => $this->_role->id,
                )
            )
        );

        $this->getElement('name')
            ->setLabel('Rename role')
            ->setValue($this->_role->name);
        $this->getElement('submit')->setLabel('change');
    }

    public function store()
    {
        try {
            $this->_role->name = $this->name->getValue();
            $this->_role->save();
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