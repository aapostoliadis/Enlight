<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $$Id$$
 * @author     Heiner Lohaus
 * @author     $$Author$$
 */

/**
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Enlight_Config_Adapter extends Enlight_Class
{
	protected $_namePrefix = '';

	protected $_nameSuffix = '';

	/**
	 * @param array $config
	 */
	public function __construct(array $config)
    {
    	$this->setOptions($config);
    }

	/**
	 * @param array $options
	 * @return Enlight_Config_Adapter
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $key=>$option) {
    		switch ($key) {
    			case 'nameSuffix':
    			case 'namePrefix':
    				$this->{'_'.$key} = (string) $option;
    				break;
    			default:
					break;
    		}
    	}
		return $this;
	}
}