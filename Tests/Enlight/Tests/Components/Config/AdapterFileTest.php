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
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     h.lohaus
 * @author     $Author$
 */

/**
 * Test case
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license/new-bsd     New BSD License
 */
class Enlight_Tests_Config_AdapterFileTest extends Enlight_Components_Test_TestCase
{
    /**
     * Test case
     */
    public function testConfigFileWrite()
    {
        $adapter = new Enlight_Config_Adapter_File(array(
            'configDir' => Enlight_TestHelper::Instance()->TestPath('TempFiles'),
            'exclusiveLock' => true,
            'skipExtends' => false,
            'configType' => 'ini'
        ));
        
        $config = new Enlight_Config('test');
        $config->setData(array('test' => true));
        $config->setSection('test');

        $adapter->write($config);
    }

    /**
     * Test case
     */
    public function testConfigWriteFile()
    {
       $this->setExpectedException('Enlight_Config_Exception');

        $adapter = new Enlight_Config_Adapter_File(array(
            'configDir' => '/fail',
            'nameSuffix' => '.txt',
            'skipExtends' => false,
            'configType' => 'ini'
        ));

        $config = new Enlight_Config('test', array('adapter' => $adapter));
        $config->setData(array('test' => true));
        $config->setSection('test');
        $config->write();
    }

    /**
     * Test case
     */
    public function testConfigReadBase()
    {
       $this->setExpectedException('Enlight_Config_Exception');
        
        $adapter = new Enlight_Config_Adapter_File(array(
            'configDir' => '/fail',
            'namePrefix' => 's_',
            'skipExtends' => false,
            'configType' => 'ini'
        ));

        $config = new Enlight_Config('test', array('adapter' => $adapter));
        $config->setData(array('test' => true));
        $config->write();
    }
}