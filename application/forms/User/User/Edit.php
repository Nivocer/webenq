<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
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

        $this->addElement($this->createElement('hidden', 'id', array(
            'value' => $this->_user->id,
        )));

        $this->getElement('username')->setValue($this->_user->username);
        $this->getElement('fullname')->setValue($this->_user->fullname);
        $this->getElement('role_id')->setValue($this->_user->role_id);
        $this->getElement('submit')->setLabel('wijzigen');
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
                $this->username->addError('Deze gebruikersnaam is al in gebruik voor een andere gebruiker');
            } else {
                $this->username->addError('Er is een onbekende fout opgetreden');
            }
            return false;
        }
    }
}