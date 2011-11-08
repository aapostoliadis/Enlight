<?php
/**
 * Enlight Auth Adapter
 */
class Enlight_Components_Form_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{

	
	/**
	 * Adds a where-condition to the db-select.
	 *
	 * @param string $condition
	 * @return Enlight_Components_Auth_Adapter_DbTable
	 */
	public function addCondition($condition)
	{
		$this->getDbSelect()->where($condition);
		return $this;
	}

	/**
	 * @throws Exception
	 * @param $lockedUntilColumn
	 * @return Enlight_Components_Auth_Adapter_DbTable
	 */
	public function setLockedUntilColumn($lockedUntilColumn)
	{
		if(!is_string($lockedUntilColumn))
			throw new Exception('Wrong Data type given. Expected Data type: String but '.getType($lockedUntilColumn).' given');
		$this->lockedUntilColumn = $lockedUntilColumn;
		return $this;
	}
	
	/**
	 * Sets the expiry column method and the expiry time.
	 *
	 * @param string $expiryColumn
	 * @param int $expiry
	 * @return Enlight_Components_Auth_Adapter_DbTable
	 */
	public function setExpiryColumn($expiryColumn, $expiry=3600)
	{
		$this->expiryColumn = $expiryColumn;
		$this->expiry = $expiry;
		return $this;
	}
	
	/**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
	public function authenticate()
	{
		$result = parent::authenticate();
		if($result->isValid()) {
			$this->updateExpiry();
			$this->updateSessionId();
		}else{
			$lockAccountUntil = time() + $this->lockSeconds;
			$datetime = date('Y-m-d H:i:s', $lockAccountUntil);
			$this->setLockedUntil($datetime);
		}
		return $result;
	}
	
	/**
	 * Updates the expiration date to now.
	 */
	protected function updateExpiry()
	{
		if($this->expiryColumn === null) {
			return;
		}
		
		$this->_zendDb->update($this->_tableName, array(
			$this->expiryColumn => Zend_Date::now()
		), $this->_zendDb->quoteInto(
			$this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?',
			$this->_identity
		));
	}
	
	/**
	 * Update the session id field in the session db.
	 */
	protected function updateSessionId()
	{
		if($this->sessionId === null) {
			return;
		}
		$this->_zendDb->update($this->_tableName, array(
			$this->sessionIdColumn => $this->sessionId
		), $this->_zendDb->quoteInto(
			$this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?',
			$this->_identity
		));
		$this->_zendDb->update($this->_tableName, array(
			$this->sessionIdColumn => null
		), $this->_zendDb->quoteInto(
			$this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' != ?',
			$this->_identity
		) . ' AND ' . $this->_zendDb->quoteInto(
			$this->_zendDb->quoteIdentifier($this->sessionIdColumn, true) . ' = ?',
			$this->sessionId
		));
	}

	protected function updateLockUntilDate($date)
	{
		if($this->lockedUntilColumn === null) {
			return;
		}
		if( !preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $date) )
			throw new Exception('Wrong Data type given. Expected Data type: Datetime (0000-00-00 00:00:00)');
		$this->_zendDb->update($this->_tableName, array($this->lockedUntilColumn => $date),
			$this->_zendDb->quoteInto($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?',$this->_identity
		));
	}
	
	/**
	 * Refresh the authentication.
	 * 
	 * Checks the expiry date and the identity.
	 *
	 * @return Zend_Auth_Result
	 */
    public function refresh()
    {
    	$credential = $this->_credential;
    	$credentialColumn = $this->_credentialColumn;
    	$identity = $this->_identity;
    	$identityColumn = $this->_identityColumn;
    	$credentialTreatment = $this->_credentialTreatment;
    	
    	$expiry = Zend_Date::now()->subSecond($this->expiry);
    	$this->setCredential($expiry);
    	$this->setCredentialColumn($this->expiryColumn);
    	$expiryColumn = $this->_zendDb->quoteIdentifier($this->expiryColumn, true);
    	$this->setCredentialTreatment('IF('.$expiryColumn.'>=?, '.$expiryColumn.', NULL)');
    	
    	$this->setIdentity($this->sessionId);
    	$this->setIdentityColumn($this->sessionIdColumn);
    	    	
    	$result = parent::authenticate();

    	$this->_credential = $credential;
    	$this->_credentialColumn = $credentialColumn;
    	$this->_identity = $identity;
    	$this->_identityColumn = $identityColumn;
    	$this->_credentialTreatment = $credentialTreatment;
    	
    	if($result->isValid()) {
			$this->updateExpiry();
		}else{
			$lockAccountUntil = time() + $this->lockSeconds;
			$datetime = date('Y-m-d H:i:s', $lockAccountUntil);
			$this->setLockedUntil($datetime);
		}
		    	    	
    	return $result;
    }
    
    /**
     * Sets the session id column value.
     *
     * @param string $sessionIdColumn
     * @return Enlight_Components_Auth_Adapter_DbTable
     */
    public function setSessionIdColumn($sessionIdColumn)
    {
        $this->sessionIdColumn = $sessionIdColumn;
        return $this;
    }
    
    /**
     * Sets the session id value in the instance.
     *
     * @param string $value
     * @return Enlight_Components_Auth_Adapter_DbTable
     */
    public function setSessionId($value)
    {
        $this->sessionId = $value;
        return $this;
    }

	/**
	 * Set and Saves a date
	 *
	 * @param string $lockedUntil
	 * @return Enlight_Components_Auth_Adapter_DbTable
	 */
	public function setLockedUntil($lockedUntil)
	{
		if( !preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $lockedUntil) )
		{
			throw new Exception('Wrong Data type given. Expected Data type: Datetime (0000-00-00 00:00:00) '.$lockedUntil);
		}
		$this->lockedUntil = $lockedUntil;
		$this->updateLockUntilDate($lockedUntil);
		$this->addCondition('lockeduntil <= NOW()');
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getLockedUntil()
	{
		return $this->lockedUntil;
	}

	/**
	 * @param int $lockSeconds
	 * @return Enlight_Components_Auth_Adapter_DbTable
	 */
	public function setLockSeconds($lockSeconds)
	{
		$this->lockSeconds = $lockSeconds;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLockSeconds()
	{
		return $this->lockSeconds;
	}
}