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
 * @covers     Enlight_Plugin_Bootstrap
 */
class Enlight_Tests_Plugin_BootstrapTest extends Enlight_Components_Test_TestCase
{

    /**
     * @var Enlight_Plugin_Bootstrap
     */
    protected $bootstrap;

    public function setUp()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $this->bootstrap = $this->getMock('Enlight_Plugin_Bootstrap', null, array('testBootstrap', $collection));
        parent::setUp();
    }

    /**
     * testCase
     */
    public function testGetName()
    {
        $this->assertEquals("testBootstrap", $this->bootstrap->getName());
    }

    /**
     * testCase
     */
    public function testSetCollection()
    {
        $collection = $this->getMock('Enlight_Plugin_PluginCollection');
        $this->bootstrap->setCollection(null);
        $this->assertNull($this->bootstrap->Collection());
        $this->bootstrap->setCollection($collection);
        $this->assertInstanceOf('Enlight_Plugin_PluginCollection', $this->bootstrap->Collection());
    }
}

