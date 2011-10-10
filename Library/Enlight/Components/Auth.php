<?php
class Enlight_Components_Auth extends Zend_Auth
{
	protected $_adapter = null;
	
	public function getAdapter()
	{
		return $this->_adapter;
	}
	
	public function setAdapter(Zend_Auth_Adapter_Interface $adapter)
	{
		$this->_adapter = $adapter;
		return $this;
	}
	
	public function authenticate(Zend_Auth_Adapter_Interface $adapter=null)
    {
    	if($adapter == null) {
    		$adapter = $this->_adapter;
    	}
    	$result = parent::authenticate($adapter);

    	if ($result->isValid()
    	  && method_exists($adapter, 'getResultRowObject')) {
    		$user = $adapter->getResultRowObject();
    		$this->getStorage()->write($user);
    	} else {
    		$this->getStorage()->clear();
    	}

       return $result;
    }
    
    public function refresh(Zend_Auth_Adapter_Interface $adapter=null)
    {
    	if($adapter == null) {
    		$adapter = $this->_adapter;
    	}
    	$result = $adapter->refresh();

    	if (!$result->isValid()) {
    		$this->getStorage()->clear();
    	}

       return $result;
    }
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
}