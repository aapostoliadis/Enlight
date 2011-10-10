<?php
class Enlight_Components_Db extends Zend_Db
{
	protected static $_adapterNamespace = 'Enlight_Components_Db_Adapter';
	
	public static function factory($adapter, $config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        /*
         * Convert Zend_Config argument to plain string
         * adapter name and separate config object.
         */
        if ($adapter instanceof Zend_Config) {
            if (isset($adapter->params)) {
                $config = $adapter->params->toArray();
            }
            if (isset($adapter->adapter)) {
                $adapter = (string) $adapter->adapter;
            } else {
                $adapter = null;
            }
        }
        
        if(empty($config['adapterNamespace'])) {
        	$config['adapterNamespace'] = self::$_adapterNamespace;
        }
        
        return parent::factory($adapter, $config);
    }
}