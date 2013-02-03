<?php
/**
 * Webenq
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
 * @package    Webenq_Application
 */
class Webenq_Session_SaveHandler implements Zend_Session_SaveHandler_Interface
{
    /**
     * Open Session - retrieve resources
     *
     * @param string $save_path
     * @param string $name
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * Close Session - free resources
     *
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     */
    public function read($id)
    {
        try {
            $record = Doctrine_Core::getTable('Webenq_Model_Session')->find($id);
            if ($record) {
                return $record->value;
            }
            return serialize(array());
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $value
     */
    public function write($id, $value)
    {
        try {
            $record = Doctrine_Core::getTable('Webenq_Model_Session')->find($id);
            if (!$record) {
                $record = new Webenq_Model_Session();
                $record->id = $id;
                $record->timestamp = date('Y-m-d H:i:s');
            }

            $record->value = $value;

            try {
                $record->save();
            } catch (Exception $e) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id)
    {
        try {
            $_SESSION = array();
            $record = Doctrine_Core::getTable('Webenq_Model_Session')->find($id);
            $record->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime)
    {
        try {
            Doctrine_Query::create()
                ->delete('Webenq_Model_Session s')
                ->where('s.timestamp < ?', date('Y-m-d H:i:s', (time() - $maxlifetime)))
                ->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}