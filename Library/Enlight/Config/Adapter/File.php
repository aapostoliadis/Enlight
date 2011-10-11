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
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Enlight_Config_Adapter_File extends Enlight_Config_Adapter
{
	/**
     * Wether to exclusively lock the file or not
     *
     * @var bool
     */
    protected $_exclusiveLock = false;

	/**
     * Whether to skip extends or not
     *
     * @var bool
     */
    protected $_skipExtends = false;

	/**
     * The config dir.
     *
     * @var string
     */
    protected $_configDir;

	/**
     * The config type.
     *
     * @var string
     */
    protected $_configType = 'ini';

	/**
     * Sets the options of an array.
     *
     * @param array $options
     * @return Enlight_Config_Adapter
     */
    public function setOptions(array $options)
    {
    	foreach ($options as $key=>$option) {
    		switch ($key) {
    			case 'exclusiveLock':
				case 'skipExtends':
    				$this->{'_'.$key} = (bool) $option;
    				break;
				case 'configDir':
				case 'configType':
    				$this->{'_'.$key} = (bool) $option;
    				break;
    			default:
					break;
    		}
    	}
    	return parent::setOptions($options);
    }

	/**
	 * Returns the complete filename by config name.
	 *
	 * @param $name
	 * @return string
	 */
	protected function getFilename($name)
    {
		$suffix = $this->_nameSuffix !== null ? $this->_nameSuffix : '.' . $this->_configType;
		$name = $this->_configDir .$this->_namePrefix . $name . $suffix;
		return $name;
	}

	/**
	 * Reads a section from the data store.
	 *
	 * @param Enlight_Config $config
	 *
	 * @internal param array|string $section
	 * @return Enlight_Config_Adapter_File
	 */
    public function read(Enlight_Config $config)
    {
		$section = $config->getSectionName();
		$name = $this->getFilename($config->getName());
		$reader = 'Zend_Config_' . ucfirst($this->_configType);
		$reader = new $reader($name, $section, array(
			'exclusiveLock' => $this->_exclusiveLock,
			'skipExtends' => $this->_skipExtends
		));
		$config->merge($reader);
		return $this;
	}

	/**
	  * Saves the data changes in the data store.
	  *
	  * @param Enlight_Config $config
	  * @return Enlight_Config_Adapter_File
	  */
    public function save(Enlight_Config $config)
    {
		$name = $this->getFilename($config->getName());
		$writer = 'Zend_Config_Writer_' . ucfirst($this->_configType);
		$writer = new $writer(array(
			'config' => $config,
            'filename' => $name
		));
		$writer->write();
		return $this;
	}
}