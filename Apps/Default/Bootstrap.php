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
        $this->loadResource('Subscriber');

        //$this->getResource('Subscriber')->registerListener(new Enlight_Event_EventHandler('test', 'strlen', 100));
        //$this->getResource('Subscriber')->registerListener(new Enlight_Event_EventHandler('test', 'strlen', 200));
        //$this->getResource('Subscriber')->registerListener(new Enlight_Event_EventHandler('test2', 'strlen', 200));

        //$config = new Enlight_Config('test', true);
        //$config->test = true;
        //$config->write();

        $adapter = $this->getResource('ConfigAdapterDbTable');

        $config = new Enlight_Config('test', array(
            'adapter' => $adapter,
            'section' => 'test',
            'allowModifications' => true
        ));
        //$config->test = 2;
        //$config->write();
        
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
     * @return Enlight_Event__SubscriberDefault
     */
    public function initSubscriber()
    {
        $subscriber = new Enlight_Event_Subscriber_Config(array(
            'section' => 'production',
            'storageAdapter' => $this->getResource('ConfigAdapter')
        ));
        $this->Application()->Events()->addSubscriber($subscriber);
        return $subscriber;
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
