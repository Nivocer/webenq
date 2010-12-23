<?php
class ZendDoctrine_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
{
	private $_model;
	private $_identityColumn;
	private $_credentialColumn;
	private $_encryptionMethod;
	private $_identity;
	private $_credential;
	private $_result;
	
	/**
	 * Class constructor
	 * 
	 * @param string $modelName Name of the model
	 * @param string $identityColumn Name of the identity column
	 * @param string $credentialColumn Name of the credential column
	 * @param string $encryptionMethod Name of the method for credential encryption
	 */
	public function __construct($modelName, $identityColumn, $credentialColumn, $encryptionMethod)
	{
		$this->setModelName($modelName);
		$this->setIdentityColumn($identityColumn);
		$this->setCredentialColumn($credentialColumn);
		$this->setEncryptionMethod($encryptionMethod);
	}

	public function setModelName($name)
	{
		$this->_model = $name;
		return $this;
	}

	public function setIdentityColumn($name)
	{
		$this->_identityColumn = $name;
		return $this;
	}

	public function setCredentialColumn($name)
	{
		$this->_credentialColumn = $name;
		return $this;
	}

	public function setEncryptionMethod($method)
	{
		$this->_encryptionMethod = $method;
		return $this;
	}

	public function setIdentity($user)
	{
		$this->_identity = $user;
		return $this;
	}
	
	public function setCredential($password)
	{
		switch ($this->_encryptionMethod) {
			case 'MD5':
				$this->_credential = md5($password);
				break;
			default:
				throw new Exception('Not a valid encryption method given');	
		}
		return $this;
	}

	public function authenticate()
	{
		$row = Doctrine_Query::create()
		     ->from($this->_model . ' u')
		     ->where('u.' . $this->_identityColumn . ' = ? AND u.' . $this->_credentialColumn . ' = ?', array($this->_identity, $this->_credential))
		     ->fetchOne();

		$authResult = array(
			'code' => Zend_Auth_Result::FAILURE,
			'identity' => $this->_identity,
			'messages' => array()
		);

		if ($row) {
			$authResult['code']= Zend_Auth_Result::SUCCESS;
			$this->_result = $row;
		}

		return new Zend_Auth_Result($authResult['code'], $authResult['identity'], $authResult['messages']);
	}
	
	public function getResult()
	{
		return $this->_result;
	}

	public function getFilteredResult($returnColumns = null, $omitColumns = null)
	{
        if (!$this->_result) {
            return false;
        }

        $returnObject = new stdClass();

        if (null !== $returnColumns) {

            $availableColumns = array_keys($this->_result->toArray());
            foreach ((array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $this->_result[$returnColumn];
                }
            }
            return $returnObject;

        } elseif (null !== $omitColumns) {

            $omitColumns = (array) $omitColumns;
            foreach ($this->_result as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue;
                }
            }
            return $returnObject;

        } else {

            foreach ($this->_result as $resultColumn => $resultValue) {
                $returnObject->{$resultColumn} = $resultValue;
            }
            return $returnObject;

        }
	}
}
