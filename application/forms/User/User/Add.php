<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_User_User_Add extends Zend_Form
{
    public function init()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->setAction("$baseUrl/user/user");

        $this->addElements(array(
            $this->createElement('text', 'username', array(
                'label' => 'username',
                'required' => true,
                'filters' => array(
                    'StringToLower',
                ),
                'validators' => array(
                    new Zend_Validate_Alpha(true),
                ),
                'maxlength' => 64,
                'size' => 20,
            )),
            $this->createElement('password', 'password', array(
                'label' => 'password',
                'required' => true,
                'maxlength' => 64,
                'size' => 20,
            )),
            $this->createElement('password', 'repeat_password', array(
                'label' => 'repeat password',
                'required' => true,
                'validators' => array(
                    new Zend_Validate_Identical(
                        Zend_Controller_Front::getInstance()
                            ->getRequest()->getPost('password')
                    ),
                ),
                'maxlength' => 64,
                'size' => 20,
            )),
            $this->createElement('text', 'fullname', array(
                'label' => 'full name',
                'required' => true,
                'validators' => array(
                    new Zend_Validate_Alpha(true),
                ),
                'maxlength' => 64,
                'size' => 20,
            )),
            $this->createElement('select', 'role_id', array(
                'label' => 'role',
                'required' => true,
                'multiOptions' => Webenq_Model_Role::getAllAsArray(),
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'add',
            )),
        ));
    }

    public function store()
    {
        try {
            $values = $this->getValues();
            $user = new Webenq_Model_User();
            $user->fromArray($values);
            $user->password = md5($values['password']);
            $user->api_key =md5($values['username'].$values['password']);
            $user->save();
            return true;
        }
        catch (Doctrine_Connection_Mysql_Exception $e) {
            if ($e->getCode() == 23000) {
                $this->username->addError('This username is already used by another user');
            } else {
                $this->username->addError('An unknown error occurred');
            }
            return false;
        }
    }
}