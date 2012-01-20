<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Bootstrap
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     Marcel Schmaeing
 * @author     $Author$
 */

/**
 * Blog Bootstrap
 * creates and loads all needed configuration resources for the Blog Application
 * For example it will initialize the db adapter or the standard zend configuration.
 * Place your session handling in here
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Bootstrap
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Bootstrap extends Enlight_Bootstrap
{
    /**
     * startup method to load all the resources
     * will be called by the Application starter
     *
     * @return mixed
     */
    public function run()
    {
        $front = $this->getResource('Front');
        try {
            $this->loadResource('ConfigAdapter');
            $this->loadResource('Extensions');
        }
        catch (Exception $e) {
            if ($front->throwExceptions()) {
                throw $e;
            }
            $front->Response()->setException($e);
        }
        return $front->dispatch();
    }

    /**
     * prepares the session for future use
     *
     * @return Enlight_Components_Session_Namespace
     */
    public function initSession()
    {
        //implement here the preferred session management
        $namespace = new Enlight_Components_Session_Namespace('Enlight');
        return $namespace;
    }

    /**
     * initialize and creates the db adapter
     *
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function initDb()
    {
        //loads the db application option given in the application starter
        $config = $this->Application()->getOptions();

        $db = Enlight_Components_Db::factory(new Zend_Config(array('adapter' => $config["adapter"], 'params' => $config["db"])));
        $db->getConnection();
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        return $db;
    }

    /**
     * loads the core extensions and register all needed namespaces
     *
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
     * loads the zend and the db resource
     * calls internally the initZend() and initDb() method
     *
     * @return Enlight_Config_Adapter_File
     */
    public function initConfigAdapterDbTable()
    {
        //calls the initDb method
        $this->loadResource('Zend');
        //calls the initDb method
        $this->loadResource('Db');
        $adapter = new Enlight_Config_Adapter_DbTable(array('automaticSerialization' => true, 'namePrefix' => 'config_',));
        Enlight_Config::setDefaultAdapter($adapter);
        return $adapter;
    }

    /**
     * loads and creates the Enlight_Config based on the config
     * file placed in the config directory
     *
     * @return Enlight_Config_Adapter_File
     */
    public function initConfigAdapter()
    {
        $adapter = new Enlight_Config_Adapter_File(array('configType' => 'ini', 'configDir' => $this->Application()->AppPath('Configs')));
        Enlight_Config::setDefaultAdapter($adapter);
        return $adapter;
    }
}
