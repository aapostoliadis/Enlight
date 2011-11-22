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
class Enlight_Tests_Config_AdapterTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Config_Adapter
     */
    protected $adapterTester;

    /**
     * @var Enlight_Config
     */
    protected $configTester;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();

        $path = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->adapterTester = new Enlight_Config_Adapter_File(array(
            'configType' => 'ini',
            'configDir' => $path
        ));

        $this->configTester = new Enlight_Config('test', array(
            'adapter' => $this->adapterTester,
            'section' => 'test'
        ));
        $this->configTester
             ->setAllowModifications()
             ->set('test', true)
             ->setReadOnly();
        $this->configTester->write();
    }

    /**
     * Test case
     */
    public function testConfigName()
    {
    	$config = new Enlight_Config('test');
		$this->assertEquals('test', $config->getName());
    }

    /**
	 * Test case
	 */
	public function testSetAdapter()
    {
        $config = new Enlight_Config('test', array('adapter' => $this->adapterTester));
        $this->assertEquals($this->adapterTester, $config->getAdapter());
    }

    /**
	 * Test case
	 */
	public function testSetDefaultAdapter()
    {
        Enlight_Config::setDefaultAdapter($this->adapterTester);
        $this->assertEquals($this->adapterTester, Enlight_Config::getDefaultAdapter());
    }

    /**
	 * Test case
	 */
	public function testForeach()
    {
        $config = new Enlight_Config('test', array(
            'adapter' => $this->adapterTester,
            'section' => 'test'
        ));
        foreach($config as $value) {
            $this->assertNotEmpty($value);
        }
    }

    /**
     * Test case
     */
    public function testConfigNameSuffix()
    {
        $dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $adapter = new Enlight_Config_Adapter_File(array(
            'configDir' => $dir,
            'nameSuffix' => '.txt',
            'skipExtends' => false,
            'configType' => 'ini'
        ));

        $config = new Enlight_Config('test', array('adapter' => $adapter));
        $config->setData(array('test' => true));
        $config->setSection('test');
        $config->write();

        $this->assertFileExists($dir . 'test' . '.txt');
    }

    /**
     * Test case
     */
    public function testConfigNamePrefix()
    {
        $dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $adapter = new Enlight_Config_Adapter_File(array(
            'configDir' => $dir,
            'namePrefix' => 's_',
            'skipExtends' => false,
            'configType' => 'ini'
        ));

        $config = new Enlight_Config('test', array('adapter' => $adapter));
        $config->setData(array('test' => true));
        $config->write();

        $this->assertFileExists($dir . 's_test' . '.ini');
    }
}