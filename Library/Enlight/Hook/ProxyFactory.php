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
 * The Enlight_Hook_ProxyFactory is the factory for the class proxies.
 * If a class is hooked, a proxy will be generated for this class.
 * The generated class extends the origin class and implements the Enlight_Hook interface.
 * Instead of the origin methods, the registered hook handler methods will be executed.
 *
 * @category   Enlight
 * @package    Enlight_Hook
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Hook_ProxyFactory extends Enlight_Class
{
    /**
     * @var null|string namespace of the proxy
     */
    protected $proxyNamespace;

    /**
     * @var directory of the proxy
     */
    protected $proxyDir;

    /**
     * @var string extension of the hook files.
     */
    protected $fileExtension = '.php';

    /**
     * Standard Constructor method.
     * If no namespace is given, the default namespace _Proxies is used.
     * If no proxy directory is given, the default directory Proxies is used.
     *
     * @param null $proxyNamespace
     * @param null $proxyDir
     */
    public function __construct($proxyNamespace = null, $proxyDir = null)
    {
        if ($proxyNamespace === null) {
            $proxyNamespace = Enlight_Application::Instance()->App() . '_Proxies';
        }
        if ($proxyDir === null) {
            $proxyDir = Enlight_Application::Instance()->AppPath('Proxies');
        }
        $this->proxyNamespace = $proxyNamespace;
        $this->proxyDir = $proxyDir;
    }

    /**
     * Return proxy class for the given class.
     *
     * @param string $class
     * @return string
     */
    public function getProxy($class)
    {
        $proxyFile = $this->getProxyFileName($class);
        $proxy = $this->getProxyClassName($class);

        if (!is_readable($proxyFile)) {
            if (!is_writable($this->proxyDir)) {
                return $class;
            }
            $content = $this->generateProxyClass($class);
            $this->writeProxyClass($proxyFile, $content);
        } else {
            $hooks = array_keys(Enlight_Application::Instance()->Hooks()->getHooks($class));
            $methodes = call_user_func($proxy . '::getHookMethods');
            $diff = array_diff($hooks, $methodes);
            if (!empty($diff)) {
                @unlink($proxyFile);
            }
        }

        return $proxy;
    }

    /**
     * Return proxy class name
     *
     * @param string $class
     * @return string
     */
    public function getProxyClassName($class)
    {
        return $this->proxyNamespace . '_' . $this->formatClassName($class);
    }

    /**
     * Format class name
     *
     * @param string $class
     * @return string
     */
    public function formatClassName($class)
    {
        return str_replace(array('_', '\\'), '', $class) . 'Proxy';
    }

    /**
     * Return proxy file name
     *
     * @param string $class
     * @return string
     */
    public function getProxyFileName($class)
    {
        $proxyClassName = $this->formatClassName($class);
        return $this->proxyDir . $proxyClassName . $this->fileExtension;
    }

    /**
     * Generate proxy class
     *
     * @param string $class
     * @return mixed|string
     */
    protected function generateProxyClass($class)
    {
        $methods = $this->generateMethods($class);
        $proxyClassName = $this->formatClassName($class);

        $search = array(
            '<namespace>',
            '<proxyClassName>',
            '<className>',
            '<methods>',
            '<arrayHookMethods>'
        );
        $replace = array(
            $this->proxyNamespace,
            $proxyClassName,
            $class,
            $methods['methods'],
            str_replace("\n", '', var_export($methods['array'], true))
        );

        $file = $this->proxyClassTemplate;
        $file = str_replace($search, $replace, $file);

        return $file;
    }

    /**
     * Write proxy class
     *
     * @param string $fileName
     * @param string $content
     * @return bool
     */
    protected function writeProxyClass($fileName, $content)
    {
        $oldMask = umask(0);
        if (!file_put_contents($fileName, $content)) {
            umask($oldMask);
            throw new Enlight_Exception('Unable to write file "' . $fileName . '"');
            return false;
        }
        chmod($fileName, 0644);
        umask($oldMask);
    }

    /**
     * Generate proxy methods
     *
     * @param unknown_type $class
     * @return unknown
     */
    protected function generateMethods($class)
    {
        $rc = new ReflectionClass($class);
        $methodsArray = array();
        $methods = '';
        foreach ($rc->getMethods() as $rm) {
            if ($rm->isFinal() || $rm->isStatic() || $rm->isPrivate()) {
                continue;
            }
            if (substr($rm->getName(), 0, 2) == '__') {
                continue;
            }
            if (!Enlight_Application::Instance()->Hooks()->hasHooks($class, $rm->getName())) {
                continue;
            }
            $methodsArray[] = $rm->getName();
            $params = '';
            $proxy_params = '';
            $array_params = '';
            foreach ($rm->getParameters() as $rp) {
                if ($params) {
                    $params .= ', ';
                    $proxy_params .= ', ';
                    $array_params .= ', ';
                }
                if ($rp->isPassedByReference()) {
                    $params .= '&';
                }
                $params .= '$' . $rp->getName();
                $proxy_params .= '$' . $rp->getName();
                $array_params .= '\'' . $rp->getName() . '\'=>$' . $rp->getName();
                if ($rp->isOptional()) {
                    $params .= ' = ' . str_replace("\n", '', var_export($rp->getDefaultValue(), true));
                }
            }
            $modifiers = Reflection::getModifierNames($rm->getModifiers());
            $modifiers = implode(' ', $modifiers);
            $search = array(
                '<methodName>',
                '<methodModifiers>',
                '<methodParameters>',
                '<proxyMethodParameters>',
                '<arrayMethodParameters>',
                '<className>'
            );
            $replace = array($rm->getName(), $modifiers, $params, $proxy_params, $array_params, $class);
            $method = $this->proxyMethodTemplate;
            $method = str_replace($search, $replace, $method);
            $methods .= $method;
        }
        return array('array' => $methodsArray, 'methods' => $methods);
    }

    /**
     * @var string Default proxy class template.
     */
    protected $proxyClassTemplate =
'<?php
class <namespace>_<proxyClassName> extends <className> implements Enlight_Hook_Proxy
{
    public function excuteParent($method, $args=null)
    {
        return call_user_func_array(array($this, \'parent::\'.$method), $args);
    }

    public static function getHookMethods()
    {
        return <arrayHookMethods>;
    }
    <methods>
}
';
    /**
     * @var string Default proxy method template
     */
    protected $proxyMethodTemplate =
'
    <methodModifiers> function <methodName>(<methodParameters>)
    {
        if(!Enlight_Application::Instance()->Hooks()->hasHooks(\'<className>\', \'<methodName>\')) {
            return parent::<methodName>(<proxyMethodParameters>);
        }
        
        $obj_args = new Enlight_Hook_HookArgs($this, \'<methodName>\', array(<arrayMethodParameters>));
        
        return Enlight_Application::Instance()->Hooks()->executeHooks($obj_args);
    }
';
}