<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
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