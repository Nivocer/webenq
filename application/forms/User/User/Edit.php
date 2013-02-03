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
class Webenq_Form_User_User_Edit extends Webenq_Form_User_User_Add
{
    protected $_user;

    public function __construct(Webenq_Model_User $user, $options = null)
    {
        $this->_user = $user;
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
                    'value' => $this->_user->id,
                )
            )
        );

        $this->getElement('username')->setValue($this->_user->username);
        $this->getElement('fullname')->setValue($this->_user->fullname);
        $this->getElement('role_id')->setValue($this->_user->role_id);
        $this->getElement('submit')->setLabel('change');
    }

    public function isValid($data)
    {
        if (!$data['password'] && !$data['repeat_password']) {
            $this->getElement('password')
                ->setRequired(false);
            $this->getElement('repeat_password')
                ->setRequired(false)
                ->removeValidator('Identical');
        }

        return parent::isValid($data);
    }

    public function store()
    {
        $values = $this->getValues();
        if (!$values['password'] || !$values['repeat_password']) {
            unset($values['password']);
            unset($values['repeat_password']);
        }

        try {
            $this->_user->fromArray($values);
            $this->_user->password = md5($values['password']);
            $this->_user->api_key =md5($this->_user->username.$this->_user->password);
            $this->_user->save();
            return true;
        }
        catch (Doctrine_Connection_Mysql_Exception $e) {
            if ($e->getCode() == 23000) {
                $this->username->addError('This name is already in use for a different user');
            } else {
                $this->username->addError('Unknown error occured');
            }
            return false;
        }
    }
}