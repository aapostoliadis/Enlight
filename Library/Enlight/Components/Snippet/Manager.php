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
 * @package    Enlight_Components_Snippet
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Components_Snippet
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Snippet_Manager extends Enlight_Class
{
    /**
     * @var     Enlight_Config_Adapter
     */
	protected $adapter;

    /**
     * @var     array
     */
	protected $namespaces;

    /**
	 * @var     string Default config class
	 */
	protected $defaultNamespaceClass = 'Enlight_Components_Snippet_Namespace';

    /**
     * @var     bool
     */
    protected $ignore_namespace;

    /**
     * @param   array|Enlight_Config_Adapter|null $options
     */
    public function __construct($options = null)
    {
        if(!is_array($options)) {
            $options = array('adapter' => $options);
        }
        if(isset($options['adapter']) && $options['adapter'] instanceof Enlight_Config_Adapter) {
            $this->adapter = $options['adapter'];
        }
    }

	/**
	 * Returns snippet model instance
	 *
	 * @param   string $namespace
	 * @return  Enlight_Components_Snippet_Namespace
	 */
	public function getNamespace($namespace)
	{
        if(!isset($this->namespaces[$namespace])) {
            $this->namespaces[$namespace] = new $this->defaultNamespaceClass($namespace, array(
                'adapter' => $this->adapter
            ));
        }
        return $this->namespaces[$namespace];
	}

    /**
     * @return Enlight_Components_Snippet_Manager
     */
    public function write()
    {
        /** @var $namespace Enlight_Components_Snippet_Namespace */
        foreach($this->namespaces as $namespace) {
            $namespace->write();
        }
        return $this;
    }
}