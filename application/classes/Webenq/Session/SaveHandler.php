<?php
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