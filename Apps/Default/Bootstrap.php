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
        /** @var $front Enlight_Controller_Front */
        $front = $this->getResource('Front');

        try {
            $this->loadResource('Zend');
            $this->loadResource('Symfony');
            $this->loadResource('Zend');
            $this->loadResource('ConfigAdapter');
            $this->loadResource('Extensions');
        } catch(Exception $e) {
            $front->Response()->setException($e);
        }

        return $front->dispatch();
    }

    /**
     * @return Enlight_Components_Session_Namespace
     */
    public function initSession()
    {
        $configSession = array_merge(array(
            'name' => 'ENLIGHTSID',
            //'save_handler' => 'db',
            //'cookie_path' => $path,
            //'cookie_domain' => $host,
            'cookie_lifetime' => 0,
            'use_trans_sid' => 0,
            'gc_probability' => 1,
        ), (array) $this->Application()->getOption('session'));

       	Enlight_Components_Session::start($configSession);
       	$this->registerResource('SessionId', Enlight_Components_Session::getId());

   		$namespace = new Enlight_Components_Session_Namespace('Default');
        return $namespace;
    }

    /**
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function initDb()
    {
        $configDb = array_merge(array(
            'adapter' => 'PDO_MYSQL',
            'params'  => array(
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'enlight'
            )
        ), (array) $this->Application()->getOption('db'));

    	$db = Enlight_Components_Db::factory(new Enlight_Config($configDb));
    	$db->getConnection();

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
     * Init symfony method
     *
     * @return bool
     */
    protected function initSymfony()
    {
        $this->Application()->Loader()->registerNamespace('Symfony', 'Symfony/');
        return true;
    }

    /**
     * @return Enlight_Config_Adapter_File
     */
    public function initConfigAdapter()
    {
        $adapter =  new Enlight_Config_Adapter_File(array(
            'configType' => 'ini',
            'configDir' => $this->Application()->AppPath('Configs')
        ));
        Enlight_Config::setDefaultAdapter($adapter);
        return $adapter;
    }
}
