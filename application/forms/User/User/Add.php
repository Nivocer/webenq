<?php
class Webenq_Form_User_User_Add extends Zend_Form
{
	public function init()
	{
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$this->setAction("$baseUrl/user/user");
		
		$this->addElements(array(
			$this->createElement('text', 'username', array(
				'label' => 'Gebruikersnaam:',
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
				'label' => 'Wachtwoord:',
				'required' => true,
				'maxlength' => 64,
				'size' => 20,
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
				'maxlength' => 64,
				'size' => 20,
			)),
			$this->createElement('text', 'fullname', array(
				'label' => 'Volledige naam:',
				'required' => true,
				'validators' => array(
					new Zend_Validate_Alpha(true),
				),
				'maxlength' => 64,
				'size' => 20,
			)),
			$this->createElement('select', 'role_id', array(
				'label' => 'Rol:',
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
    		$values = $this->getValues();
    		$user = new User();
    		$user->fromArray($values);
    		$user->password = md5($values['password']);
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