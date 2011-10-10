<?php
/**
 * Enlight Mysql Db Adapter
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Components
 */
class Enlight_Components_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql
{
	/**
     * Quote a raw string.
     *
     * @param string $value
     * @return string
     */
	protected function _quote($value)
    {
    	if($value instanceof Zend_Date) {
    		$value = $value->toString('YYYY-MM-dd HH:mm:ss');
    	}
    	return parent::_quote($value);
    }
    
    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement_Pdo
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    public function query($sql, $bind = array())
    {
        if (empty($bind) && $sql instanceof Zend_Db_Select) {
            $bind = $sql->getBind();
        }

        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if($value instanceof Zend_Date) {
		    		$bind[$name] = $value->toString('YYYY-MM-dd HH:mm:ss');
		    	}
            }
        }
        
        return parent::query($sql, $bind);
    }
    
    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
            return;
        }
        
        try {
        	parent::_connect();
        } catch (Exception $e) {
        	$message = $e->getMessage();
        	$message = str_replace(array(
        		$this->_config['username'],
        		$this->_config['password']
        	), '******', $message);
            throw new Zend_Db_Adapter_Exception($message, $e->getCode(), $e->getPrevious());
        }
        
        // finally, we delete the authorization data
        unset($this->_config['username'], $this->_config['password']);
    }
}