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
 * @package    Enlight_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Bootstrap is responsible to manage the application resources.
 *
 * To load the resources the bootstrap used the application configuration which could
 * contains the database connection data.
 *
 * @category   Enlight
 * @package    Enlight_Application
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
abstract class Enlight_Bootstrap extends Enlight_Class implements Enlight_Hook
{
    /**
     * Constant for the bootstrap status, set before the resource will be initial
     */
    const STATUS_BOOTSTRAP = 0;

    /**
     * Constant for the bootstrap status, set after the resource successfully initialed
     */
    const STATUS_LOADED = 1;

    /**
     * Constant for the bootstrap status, set if an exception thrown by the initialisation
     */
    const STATUS_NOT_FOUND = 2;

    /**
     * Constant for the bootstrap status, set when the resource registered.
     */
    const STATUS_ASSIGNED = 3;

    /**
     * Property which contains all registered resources
     *
     * @var array
     */
    protected $resourceList = array();

    /**
     * Property which contains all states for the registered resources.
     *
     * @var array
     */
    protected $resourceStatus = array();

    /**
     * Instance of the enlight application.
     *
     * @var Enlight_Application
     */
    protected $application;

    /**
     * The class constructor sets the instance of the given enlight application into
     * the internal $application property.
     *
     * @param Enlight_Application $application
     */
    public function __construct(Enlight_Application $application)
    {
        $this->setApplication($application);
        parent::__construct();
        //$options = $application->getOptions();
        //$this->setOptions($options);
    }

    /**
     * Sets the application instance into the internal $application property.
     *
     * @param  Enlight_Application $application
     * @return Enlight_Bootstrap
     */
    public function setApplication(Enlight_Application $application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Returns the application instance.
     *
     * @return Enlight_Application
     */
    public function Application()
    {
        return $this->application;
    }

    /**
     * The run method loads the Enlight_Controller_Front resource and run the dispatch function.
     *
     * @return mixed
     */
    public function run()
    {
        /** @var $front Enlight_Controller_Front */
        $front = $this->getResource('Front');
        return $front->dispatch();
    }

    /**
     * Loads the Zend resource and initials the Enlight_Controller_Front class.
     * After the front resource loaded, the controller path will be added to the
     * front dispatcher. After the controller path is set to the dispatcher,
     * the plugin namespace of the front resource will be set.
     *
     * @return Enlight_Controller_Front
     */
    protected function initFront()
    {
        $this->loadResource('Zend');

        /** @var $front Enlight_Controller_Front */
        $front = Enlight_Class::Instance('Enlight_Controller_Front');

        $front->Dispatcher()->addModuleDirectory(
            $this->Application()->AppPath('Controllers')
        );

        $config = $this->Application()->getOption('Front');
        if ($config !== null) {
            $front->setParams($config);
        }

        $this->Application()->Plugins()->registerNamespace($front->Plugins());

        $front->setParam('bootstrap', $this);

        if (!empty($config['throwExceptions'])) {
            $front->throwExceptions(true);
        }
        if (!empty($config['returnResponse'])) {
            $front->returnResponse(true);
        }

        return $front;
    }

    /**
     * The init template method instantiates the Enlight_Template_Manager
     * and sets the cache, template and compile directory into the manager.
     * After the directories has been set, the template configuration will be set
     * from the internal config array.
     *
     * @return Enlight_Template_Manager
     */
    protected function initTemplate()
    {
        /** @var $template Enlight_Template_Manager */
        $template = Enlight_Class::Instance('Enlight_Template_Manager');

        $template->setCompileDir($this->Application()->AppPath('Cache_Compiles'));
        $template->setCacheDir($this->Application()->AppPath('Cache_Templates'));
        $template->setTemplateDir($this->Application()->AppPath('Views'));

        $config = $this->Application()->getOption('template');
        if ($config !== null) {
            foreach ($config as $key => $value) {
                $template->{'set' . $key}($value);
            }
        }

        return $template;
    }

    /**
     * Registers the Zend namespace.
     *
     * @return bool
     */
    protected function initZend()
    {
        $this->Application()->Loader()->registerNamespace('Zend', 'Zend/');
        //$this->Application()->Loader()->addIncludePath(
        //        $this->Application()->Path(), Enlight_Loader::POSITION_PREPEND
        //);
        return true;
    }

    /**
     * Add the given resource to the internal resource list and set the STATUS_ASSIGNED status.
     * The given name will be used as identifier.
     *
     * @param string $name
     * @param mixed $resource
     * @return Enlight_Bootstrap
     */
    public function registerResource($name, $resource)
    {
        $this->resourceList[$name] = $resource;
        $this->resourceStatus[$name] = self::STATUS_ASSIGNED;
        return $this;
    }

    /**
     * Checks if the given resource name already registered. If not the resource will be loaded.
     *
     * @param string $name
     * @return bool
     */
    public function hasResource($name)
    {
        return isset($this->resourceList[$name]) || $this->loadResource($name);
    }

    /**
     * Checks if the given resource name already registered.
     * Unlike as the hasResource method is, if the resource does not exist the resource will not even loaded.
     *
     * @param string $name
     * @return bool
     */
    public function issetResource($name)
    {
        return isset($this->resourceList[$name]);
    }

    /**
     * Getter method for a single resource. If the source not already registered, this function will
     * load the resource automatically. In case the resource is not found the status STATUS_NOT_FOUND will
     * be set and an Enlight_Exception will be thrown.
     *
     * @param string $name
     * @return Enlight_Class
     */
    public function getResource($name)
    {
        if (!isset($this->resourceStatus[$name])) {
            $this->loadResource($name);
        }
        if ($this->resourceStatus[$name] === self::STATUS_NOT_FOUND) {
            throw new Enlight_Exception('Resource "' . $name . '" not found failure');
        }
        return $this->resourceList[$name];
    }

    /**
     * Loads the given resource. If the resource is already registered and the status
     * is STATUS_BOOTSTRAP an Enlight_Exception is thrown.
     * The resource is initial by the Enlight_Bootstrap_InitResource event.
     * If this event don't exist for the given resource, the resource is initial
     * by call_user_func.
     * After the resource initialed the event Enlight_Bootstrap_AfterInitResource will
     * be fired. In case an exception will be thrown by initializing the resource,
     * Enlight sets the status STATUS_NOT_FOUND for the resource in the resource status list.
     * In case the resource successfully initialed the resource have the status STATUS_LOADED
     *
     * @param string $name
     * @return bool
     */
    public function loadResource($name)
    {
        if (isset($this->resourceStatus[$name])) {
            switch ($this->resourceStatus[$name]) {
                case self::STATUS_BOOTSTRAP:
                    throw new Enlight_Exception('Resource "' . $name . '" can\'t resolve all dependencies');
                case self::STATUS_NOT_FOUND:
                    return false;
                case self::STATUS_ASSIGNED:
                case self::STATUS_LOADED:
                    return true;
                default:
                    break;
            }
        }

        try {
            $this->resourceStatus[$name] = self::STATUS_BOOTSTRAP;
            if ($event = $this->Application()->Events()->notifyUntil(
                            'Enlight_Bootstrap_InitResource_' . $name, array('subject' => $this)
            )
            ) {
                $this->resourceList[$name] = $event->getReturn();
            } elseif (method_exists($this, 'init' . $name)) {
                $this->resourceList[$name] = call_user_func(array($this, 'init' . $name));
            }
            $this->Application()->Events()->notify(
                'Enlight_Bootstrap_AfterInitResource_' . $name, array('subject' => $this)
            );
        }
        catch (Exception $e) {
            $this->resourceStatus[$name] = self::STATUS_NOT_FOUND;
            throw $e;
        }

        if (isset($this->resourceList[$name]) && $this->resourceList[$name] !== null) {
            $this->resourceStatus[$name] = self::STATUS_LOADED;
            return true;
        } else {
            $this->resourceStatus[$name] = self::STATUS_NOT_FOUND;
            return false;
        }
    }

    /**
     * If the given resource is set, the resource and the resource status will be removed from the
     * list properties.
     *
     * @param string $name
     * @return Enlight_Bootstrap
     */
    public function resetResource($name)
    {
        if (isset($this->resourceList[$name])) {
            unset($this->resourceList[$name]);
            unset($this->resourceStatus[$name]);
        }
        return $this;
    }

    /**
     * Returns called resource
     *
     * @param string $name
     * @param array $arguments
     * @return Enlight_Class Resource
     */
    public function __call($name, $arguments = null)
    {
        return $this->getResource($name);
    }
}