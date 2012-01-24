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
 * @author     Oliver Denter
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
class Enlight_Tests_Plugin_PluginManagerTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Plugin_PluginManager
     */
    protected $manager;

    public function setUp()
    {
        $app = $this->getMock('Enlight_Application', null, array("TestApp"), '', false);
        $this->manager = $this->getMock('Enlight_Plugin_PluginManager', null, array($app));
        $this->manager->setApplication($app);
        parent::setUp();
    }

    public function testRegisterNamespace()
    {
        $namespace = $this->getMock('Enlight_Plugin_Namespace', array('registerNamespace'), array("myNamespace"));
        $this->manager->registerNamespace($namespace);
        $getNamespace = $this->manager->get('myNamespace');
        $this->assertInstanceOf('Enlight_Plugin_Namespace', $getNamespace);
        $this->assertEquals('myNamespace', $getNamespace->getName());
    }

    public function testRegisterPlugin()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $plugin = $this->getMock('Enlight_Plugin_Bootstrap', null, array('testBootstrap', $collection));
        $this->manager->registerPlugin($plugin);
        $this->assertInstanceOf('Enlight_Plugin_Bootstrap', $this->manager->get('testBootstrap'));
    }

    public function testLoad()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $plugin = $this->getMock('Enlight_Plugin_Bootstrap', null, array('testBootstrap', $collection));
        $this->manager->registerPlugin($plugin);
        $this->assertInstanceOf('Enlight_Plugin_PluginManager', $this->manager->load('testBootstrap'));
    }

    public function testGetIterator()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $plugin = $this->getMock('Enlight_Plugin_Bootstrap', null, array('testBootstrap', $collection));
        $this->manager->registerPlugin($plugin);
        $iterator = $this->manager->getIterator();
        $this->assertInstanceOf('ArrayObject', $iterator);
        $this->assertEquals(1, $iterator->count());
    }

    public function testReset()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $plugin = $this->getMock('Enlight_Plugin_Bootstrap', null, array('testBootstrap', $collection));
        $this->manager->registerPlugin($plugin);
        $this->manager->reset();
        $iterator = $this->manager->getIterator();
        $this->assertEquals(0, $iterator->count());
    }

    public function testLoadException()
    {
        $this->setExpectedException('Enlight_Exception', 'Plugin "test" not found failure');
        $this->manager->get('test');
    }
}

