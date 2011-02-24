<?php
class Webenq_Form_User_Role_Edit extends Webenq_Form_User_Role_Add
{
	protected $_role;
	
	public function __construct(Role $role, $options = null)
	{
		$this->_role = $role;
		parent::__construct($options);
	}
	
	public function init()
	{
		parent::init();
		
		$this->addElement(
			$this->createElement('hidden', 'id', array(
				'value' => $this->_role->id,
			))
		);
		
		$this->getElement('name')
			->setLabel('Hernoem rol:')
			->setValue($this->_role->name);
		$this->getElement('submit')->setLabel('wijzigen');
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
    			$this->name->addError('Deze naam is al in gebruik voor een andere rol');
    		} else {
    			$this->name->addError('Er is een onbekende fout opgetreden');
    		}
    		return false;
    	}
	}
}