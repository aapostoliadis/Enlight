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

        return parent::run();
    }

    /**
     * @return Enlight_Components_Session_Namespace
     */
    public function initSession()
    {
        $configSession = $this->Application()->getOption('session') ? $this->Application()->getOption('session') : array();

       	if(!empty($configSession['unitTestEnabled'])) {
       		Enlight_Components_Session::$_unitTestEnabled = true;
       	}
       	unset($configSession['unitTestEnabled']);

       	if(Enlight_Components_Session::isStarted())	{
       		Enlight_Components_Session::writeClose();
       	}

        /*
        $front = $this->getResource('Front');

       	$session_id = $this->getResource('SessionID');
       	if(!empty($session_id)) {
       		Enlight_Components_Session::setId($session_id);
       	}

       	if($this->hasResource('Front') && $front->Front()->Request()) {
       		$request = $front->Request();
       		$path = rtrim($request->getBasePath(), '/') . '/';
       		$host = $request->getHttpHost()=='localhost' ? null : '.' . $request->getHttpHost();
       	} else {
       		$config = $this->getResource('Config');
       		$path = rtrim(str_replace($config->get('Host'), '', $config->get('BasePath')),'/').'/';
       		$host = $config->get('Host')=='localhost' ? null : '.' . $config->get('Host');
       	}
        */
        
        $defaultConfig = array(
            'name' => 'SHOPWARESID',
            //'save_handler' => 'db',
            //'cookie_path' => $path,
            //'cookie_domain' => $host,
            'cookie_lifetime' => 0,
            'use_trans_sid' => 0,
            'gc_probability' => 1,
        );

        $configSession = array_merge($defaultConfig, $configSession);

       	if($configSession['save_handler'] == 'db') {
       		$config_save_handler = array(
   	    		'db'			 => $this->getResource('Db'),
   		    	'name'           => 's_core_sessions',
   		    	'primary'        => 'id',
   		    	'modifiedColumn' => 'modified',
   		    	'dataColumn'     => 'data',
   		    	'lifetimeColumn' => 'expiry'
   	    	);
   	    	Enlight_Components_Session::setSaveHandler(new Enlight_Components_Session_SaveHandler_DbTable($config_save_handler));
   	    	unset($configSession['save_handler']);
       	}

       	Enlight_Components_Session::start($configSession);

       	//$this->registerResource('SessionID', Enlight_Components_Session::getId());

   		$namespace = new Enlight_Components_Session_Namespace('Shopware');

        return $namespace;
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function initDb()
    {
    	$db = Enlight_Components_Db::factory(new Zend_Config(array(
            'adapter' => 'PDO_MYSQL',
            'params'  => array(
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'enlight'
            )
        )));
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
