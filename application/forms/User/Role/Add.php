<?php
class Webenq_Form_User_Role_Add extends Zend_Form
{
    public function init()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->setAction("$baseUrl/user/role");
        
        $this->addElements(array(
            $this->createElement('text', 'name', array(
                'label' => 'Nieuwe rol:',
                'required' => true,
                'filters' => array('StringToLower'),
                'validators' => array('Alpha'),
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'toevoegen',
            )),
        ));
    }
    
    public function store()
    {
        try {
            $role = new Role();
            $role->name = $this->name->getValue();
            $role->save();
            return true;
        }
        catch (Doctrine_Connection_Mysql_Exception $e) {
            if ($e->getCode() == 23000) {
                $this->name->addError('Deze naam is al in gebruik voor een andere rol');
            } else {
                $this->name->addError('Er is een onbekende fout opgetreden');
            }
            return false;
        }
    }
}