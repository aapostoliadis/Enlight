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
 * @covers     Enlight_Plugin_Bootstrap_Config
 */
class Enlight_Tests_Plugin_ConfigTest extends Enlight_Components_Test_TestCase
{
    /**
     * @var Enlight_Plugin_Bootstrap_Config
     */
    protected $config;

    public function setUp()
    {
        $collection = $this->getMock('Enlight_Plugin_Namespace_Config', array('getConfig'), array('namespaceConfig'), '', false);
        $collection->expects($this->once())->method('getConfig')
                   ->with($this->isType('string'))
                   ->will($this->returnValue("test3"));

        $this->config = $this->getMock('Enlight_Plugin_Bootstrap_Config', null, array('test', $collection));
        parent::setUp();
    }

    public function testConfig()
    {
//        echo "<pre>";
//        print_r($this->config->Config());
//        echo "</pre>";
//        exit();
    }

    public function testSubscribeEvent()
    {
        $this->config->subscribeEvent('PostDispatch', 'myFunction', 100);

        $this->config->expects($this->once())
                     ->method('Subscriber')
                     ->will($this->returnValue("test00"));

        echo "<pre>";
        print_r($this->config->Collection()->Subscriber()->getListeners());
        echo "</pre>";
        exit();

    }
}






