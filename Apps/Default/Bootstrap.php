<?php
class Default_Bootstrap extends Enlight_Bootstrap
{
    /**
     * Run application method
     *
     * @return mixed
     */
    public function run()
    {
        $this->loadResource('Zend');
        $this->loadResource('ConfigAdapter');
        $this->loadResource('Extensions');

//		$namespace = new Enlight_Plugin_Namespace_Config('Core');
//		$test = new Enlight_Extensions_JsonRenderer_Bootstrap($namespace,'JsonRenderer');

        return parent::run();
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function initDb()
    {
    	$db = Enlight_Components_Db::factory('PDO_MYSQL', array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'enlight'
        ));
    	$db->getConnection();
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
        return $db;
    }


    /**
     * @return Enlight_Plugin_Namespace_Config
     */
    public function initExtensions()
    {
        $namespace = new Enlight_Plugin_Namespace_Config('Core');
        $this->Application()->Plugins()->registerNamespace($namespace);
        $this->Application()->Events()->registerSubscriber($namespace->Subscriber());
        return $namespace;
    }

    /**
     * @return Enlight_Config_Adapter_File
     */
    public function initConfigAdapterDbTable()
    {
        $this->loadResource('Zend');
        $this->loadResource('Db');
        $adapter =  new Enlight_Config_Adapter_DbTable(array(
            'automaticSerialization' => true,
            'namePrefix' => 'config_',
        ));
        Enlight_Config::setDefaultAdapter($adapter);
        return $adapter;
    }

    /**
     * @return Enlight_Config_Adapter_File
     */
    public function initConfigAdapter()
    {
        $this->loadResource('Zend');
        $adapter =  new Enlight_Config_Adapter_File(array(
            'configType' => 'ini',
            'configDir' => $this->Application()->AppPath('Configs')
        ));
        Enlight_Config::setDefaultAdapter($adapter);
        return $adapter;
    }
}
