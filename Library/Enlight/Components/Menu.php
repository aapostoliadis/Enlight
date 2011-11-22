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
 * @category    Enlight
 * @package	    Enlight_Components
 * @copyright   Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license	    http://enlight.de/license	 New BSD License
 * @version	    $Id$
 * @author	    Heiner Lohaus
 * @author	    $Author$
 */

/**
 * @category    Enlight
 * @package	    Enlight_Components_Menu
 * @copyright   Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license	    http://enlight.de/license	 New BSD License
 */
class Enlight_Components_Menu extends Zend_Navigation
{
    /**
     * @var string
     */
	protected $_defaultPageClass = 'Enlight_Components_Menu_Item';

    /**
     * @var Enlight_Components_Menu_Adapter_DbTable
     */
	protected $_adapter;

    /**
     * @throws  Enlight_Exception
     * @return  Enlight_Components_Menu
     */
	public function write()
	{
		if($this->_adapter === null) {
			throw new Enlight_Exception('A save handler are required failure');
		}
		$this->_adapter->write($this);
        return $this;
	}

    /**
     * @throws  Enlight_Exception
     * @return  Enlight_Components_Menu
     */
	public function read()
	{
		if($this->_adapter===null) {
			throw new Enlight_Exception('A save handler are required failure');
		}
		$this->_adapter->read($this);
        return $this;
	}

    /**
     * @param   $adapter
     * @return  Enlight_Components_Menu
     */
	public function setAdapter($adapter)
	{
		$this->_adapter = $adapter;
        return $this;
	}

    /**
     * @param   array|Enlight_Components_Menu_Item $page
     * @return  Enlight_Components_Menu
     */
	public function addItem($page)
	{
		return $this->addPage($page);
	}

    /**
     * @param   Enlight_Config|array $pages
     * @return  Enlight_Components_Menu
     */
	public function addItems($pages)
	{
		return $this->addPages($pages);
	}

    /**
     * @param   Enlight_Config|array $pages
     * @return  Enlight_Components_Menu
     */
	public function addPages($pages)
	{
		if ($pages instanceof Zend_Config) {
            $pages = $pages->toArray();
        }
        while ($page = array_shift($pages)) {
        	if ($page instanceof Zend_Config) {
                /** @var Zend_Config $page */
        		$page = $page->toArray();
	        }
	        if(is_array($page)&&empty($page['parent'])) {
	        	unset($page['parent']);
	        }
        	if(is_array($page) && isset($page['parent'])
              && !$page['parent'] instanceof Zend_Navigation_Container) {
	        	$parent = $this->findOneBy('id', $page['parent']);
	        	if(!empty($parent)) {
	        		unset($page['parent']);
	        		$parent->addPage($page);
	        	} else {
	        		array_push($pages, $page);
	        	}
	        } else {
	        	$this->addPage($page);
	        }
        }
        return $this;
	}

    /**
     * @param   array|Enlight_Components_Menu_Item $page
     * @return  Enlight_Components_Menu
     */
	public function addPage($page)
	{
		if ($page instanceof Zend_Config) {
			$page = $page->toArray();
		}

		if(is_array($page) && isset($page['parent'])
          && !$page['parent'] instanceof Zend_Navigation_Container) {
			$page['parent'] = $this->findOneBy('id', $page['parent']);
		}

		if (is_array($page)) {
			$page = call_user_func($this->_defaultPageClass.'::factory', $page);
		}

        /** @var Zend_Navigation_Container $container */
		$container = $page->get('parent');
		if($container instanceof Zend_Navigation_Container) {
			$container->addPage($page);
		} else {
			parent::addPage($page);
		}

        return $this;
	}
}