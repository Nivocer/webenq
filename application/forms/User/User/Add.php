<?php

class HVA_Form_User_User_Add extends Zend_Form
{
	public function init()
	{
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$this->setAction("$baseUrl/user/user");
		
		$this->addElements(array(
			$this->createElement('text', 'username', array(
				'label' => 'Naam:',
				'required' => true,
				'filters' => array(
					'StringToLower',
				),
				'validators' => array(
					new Zend_Validate_Alpha(true),
				),
			)),
			$this->createElement('password', 'password', array(
				'label' => 'Wachtwoord:',
				'required' => true,
			)),
			$this->createElement('password', 'repeat_password', array(
				'label' => 'Herhaal wachtwoord:',
				'required' => true,
				'validators' => array(
					new Zend_Validate_Identical(
						Zend_Controller_Front::getInstance()
							->getRequest()->getPost('password')
					),
				),
			)),
			$this->createElement('text', 'fullname', array(
				'label' => 'Naam:',
				'required' => true,
				'validators' => array(
					new Zend_Validate_Alpha(true),
				),
			)),
			$this->createElement('select', 'role_id', array(
				'label' => 'Naam:',
				'required' => true,
				'multiOptions' => Role::getAllAsArray(),
			)),
			$this->createElement('submit', 'submit', array(
				'label' => 'toevoegen',
			)),
		));
	}
	
	public function store()
	{
    	try {
    		$user = new User();
    		$user->fromArray($this->getValues());
    		$user->save();
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