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
 * @package    Enlight_Config
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * The Enlight_Config_Adapter_File is an adapter to write the enlight configuration to a file and read this.
 * The adapter use the zend config writer.
 *
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
     * The filename suffix.
     *
     * @var string
     */
    protected $_nameSuffix;

    /**
     * Sets the options from an array.
     *
     * @param array $options
     * @return Enlight_Config_Adapter
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $option) {
            switch ($key) {
                case 'exclusiveLock':
                case 'skipExtends':
                    $this->{'_' . $key} = (bool)$option;
                    break;
                case 'configDir':
                case 'configType':
                    $this->{'_' . $key} = (string)$option;
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
        $name = $this->_configDir . $this->_namePrefix . $name . $suffix;
        return $name;
    }

    /**
     * Reads the base config from data store.
     *
     * @param   string $filename
     * @return  Enlight_Config
     */
    protected function readBase($filename)
    {
        try {
            if (file_exists($filename)) {
                $reader = 'Zend_Config_' . ucfirst($this->_configType);
                $base = new $reader($filename, null, array(
                    'skipExtends' => true,
                    'allowModifications' => true)
                );
            } else {
                $base = new Enlight_Config(array(), true);
            }
        }
        catch (Zend_Exception $e) {
            throw new Enlight_Config_Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $base;
    }

    /**
     * Reads a section from the data store.
     *
     * @param Enlight_Config $config
     * @return Enlight_Config_Adapter_File
     */
    public function read(Enlight_Config $config)
    {
        $section = $config->getSection();
        $name = $this->getFilename($config->getName());
        if (file_exists($name)) {
            $reader = 'Zend_Config_' . ucfirst($this->_configType);
            /** @var $reader Zend_Config */
            $reader = new $reader($name, $section, array(
                'skipExtends' => $this->_skipExtends)
            );
            $config->setData($reader->toArray());
        } else {
            $config->setData(array());
        }
        return $this;
    }

    /**
     * Saves the data changes to the data store.
     *
     * @param Enlight_Config $config
     * @return Enlight_Config_Adapter_File
     */
    public function write(Enlight_Config $config)
    {
        $section = $config->getSection();
        $filename = $this->getFilename($config->getName());

        if (!empty($section)) {
            $base = $this->readBase($filename);
            $base->$section = $config;
        } else {
            $base = $config;
        }

        try {
            $writer = 'Zend_Config_Writer_' . ucfirst($this->_configType);
            /** @var $writer Zend_Config_Writer */
            $writer = new $writer(array(
                'config' => $base,
                'filename' => $filename)
            );
            $writer->write();
        }
        catch (Zend_Exception $e) {
            throw new Enlight_Config_Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $this;
    }
}