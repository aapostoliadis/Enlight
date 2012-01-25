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
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Hook_HookManager allows to hook class methods.
 *
 * Class methods can be hooked by type before,
 * replace and after. It uses a proxy to hook the class on which is the hook placed,
 * so the class method can be overwritten. If a class is hooked, a proxy will be generated for this class.
 * The generated class extends the origin class and implements the Enlight_Hook_Proxy interface.
 * Instead of the origin methods, the registered hook handler methods will be executed.
 *
 * The Enlight_Hook_HookManager stored all registered hook handler, which registered
 * by the Enlight_Hook_HookSubscriber. Checks whether a class method of the current one has been hook and executes it.
 *
 * The hook arguments will passed to the handler, the proxy allows the handler to execute
 * by the manager and overwriting the return of the corresponding method.
 *
 * @category   Enlight
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Hook_HookManager extends Enlight_Class
{
    /**
     * @var null|Enlight_Hook_ProxyFactory instance of the Enlight_Hook_ProxyFactory.
     */
    protected $proxy_factory = null;

    /**
     * @var array Internal list of all registered hook handlers
     */
    protected $list = array();

    /**
     * @var array Internal list of all registered hook aliases.
     */
    protected $aliases = array();

    /**
     * Registers the given hook handler.
     * If no position is set in the handler, the handler is added to the end of the list.
     *
     * @param Enlight_Hook_HookHandler $handler
     * @return Enlight_Hook_HookManager
     */
    public function registerHook(Enlight_Hook_HookHandler $handler)
    {
        $list =& $this->list[$handler->getClass()][$handler->getMethod()][$handler->getType()];

        if ($handler->getPosition()) {
            $position = (int)$handler->getPosition();
        } else {
            $position = count($list);
        }

        while (isset($list[$position])) {
            ++$position;
        }

        $list[$position] = $handler;

        ksort($list);

        return $this;
    }

    /**
     * Register all hooks of the given hook subscriber.
     *
     * @param Enlight_Hook_HookSubscriber $subscriber
     * @return Enlight_Hook_HookManager
     */
    public function addSubscriber(Enlight_Hook_HookSubscriber $subscriber)
    {
        $hooks = $subscriber->getSubscribedHooks();
        if (!empty($hooks)) {
            foreach ($hooks as $hook) {
                $this->registerHook($hook);
            }
        }

        return $this;
    }

    /**
     * Checks if the given class have registered hooks.
     * If a method given the examination is limited to the method.
     *
     * @param      $class
     * @param null $method
     * @return bool
     */
    public function hasHooks($class, $method = null)
    {
        $class = is_object($class) ? get_class($class) : $class;
        $class = isset($this->aliases[$class]) ? $this->aliases[$class] : $class;
        if (isset($method)) {
            return isset($this->list[$class][$method]);
        } else {
            return isset($this->list[$class]);
        }
    }

    /**
     * Returns all registered hooks of the given arguments.
     *
     * @param      $class
     * @param null $method
     * @param null $type
     * @return array
     */
    public function getHooks($class, $method = null, $type = null)
    {
        $class = is_object($class) ? get_class($class) : $class;
        $class = isset($this->aliases[$class]) ? $this->aliases[$class] : $class;
        if (isset($type)) {
            return isset($this->list[$class][$method][$type]) ? $this->list[$class][$method][$type] : array();
        } elseif (isset($method)) {
            return isset($this->list[$class][$method]) ? $this->list[$class][$method] : array();
        } else {
            return isset($this->list[$class]) ? $this->list[$class] : array();
        }
    }

    /**
     * Returns the proxy for the given class. If the Enlight_Hook_ProxyFactory not
     * already has been instantiate the function instantiate it automatically.
     *
     * @param $class
     * @return mixed
     */
    public function getProxy($class)
    {
        if (!$this->proxy_factory) {
            $this->proxy_factory = new Enlight_Hook_ProxyFactory();
        }
        return $this->proxy_factory->getProxy($class);
    }

    /**
     * Checks if a proxy exist for the given class.
     *
     * @param $class
     * @return bool
     */
    public function hasProxy($class)
    {
        if (!$this->proxy_factory) {
            $this->proxy_factory = new Enlight_Hook_ProxyFactory();
        }
        return $this->proxy_factory->getProxy($class) !== $class;
    }

    /**
     * Executes all registered hooks for the given hook arguments.
     * First, all hooks of the typeBefore type executed.
     * Then the typeReplace hooks are executed.
     * If no typeReplace hook exists, the function checks if the executeParent method on the subject exists.
     * If this is the case, the executeParent function will be executed.
     * At the end the typeAfter hooks are executed.
     *
     * @param Enlight_Hook_HookArgs $args
     * @return mixed
     */
    public function executeHooks(Enlight_Hook_HookArgs $args)
    {
        $hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeBefore);
        if ($hooks) {
            foreach ($hooks as $hook) {
                $hook->execute($args);
            }
        }

        $hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeReplace);
        if ($hooks) {
            foreach ($hooks as $hook) {
                $args->setReturn($hook->execute($args));
            }
        } elseif (method_exists($args->getSubject(), 'executeParent')) {
            $args->setReturn($args->getSubject()->executeParent($args->getMethod(), $args->getArgs()));
        } elseif (method_exists($args->getSubject(), 'excuteParent')) {
            $args->setReturn($args->getSubject()->excuteParent($args->getMethod(), $args->getArgs()));
        }

        $hooks = $this->getHooks($args->getName(), $args->getMethod(), Enlight_Hook_HookHandler::TypeAfter);
        if ($hooks) {
            foreach ($hooks as $hook) {
                $hook->execute($args);
            }
        }

        return $args->getReturn();
    }

    /**
     * Sets the given name as alias for the given target.
     *
     * @param $name
     * @param $target
     * @return Enlight_Hook_HookManager
     */
    public function setAlias($name, $target)
    {
        $this->aliases[$target] = $name;
        return $this;
    }

    /**
     * Returns the alias for the given name.
     * @param $name
     * @return null
     */
    public function getAlias($name)
    {
        return isset($this->_aliases[$name]) ? $this->_aliases[$name] : null;
    }

    /**
     * Resets the aliases and registered hooks.
     * @return Enlight_Hook_HookManager
     */
    public function resetHooks()
    {
        $this->list = array();
        $this->aliases = array();
        return $this;
    }
}