<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
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