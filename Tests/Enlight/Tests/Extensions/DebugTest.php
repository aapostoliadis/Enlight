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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * Test suite
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @covers      Enlight_Extensions_Log_Bootstrap
 */
class Enlight_Tests_Extensions_Debug_BootstrapTest extends Enlight_Components_Test_Plugin_TestCase
{
    /**
     * @var Enlight_Extensions_Log_Bootstrap
     */
    protected $log;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->log = $this->getMock('Enlight_Extensions_Log_Bootstrap', null, array('Json'));
        $namespace = new Enlight_Plugin_Namespace_Loader('Extensions');
        $namespace->registerPlugin($this->log);
        parent::setUp();
    }

    /**
     * Test case
     */
    public function testInstall()
    {
        $log = $this->getMock(
            'Enlight_Extensions_Log_Bootstrap', array('subscribeEvent'),
            array(), '', false
        );
        $log->expects($this->any())
            ->method('subscribeEvent')
            ->with($this->isType('string'), $this->isType('string'), $this->anything());
        $this->assertTrue($log->install());
    }

    /**
     * Test case
     */
    public function testOnInitResourceLog()
    {
        $args = $this->createEventArgs();
        $this->assertInstanceOf('Zend_Log', $this->log->onInitResourceLog($args));
    }

    /**
     * Test case
     */
    public function testOnRouteStartup()
    {
        $request = $this->Request();
        $request->setHeader('User-Agent', 'Enlight');
        $front = $this->Front()->setRequest($request);
        $args = $this->createEventArgs(array('subject' => $front));
        $this->assertEquals(null, $this->log->onRouteStartup($args));
    }

    /**
     * Test case
     */
    public function testOnDispatchLoopShutdown()
    {
        $channel = $this->log->FirebugChannel();
        $args = $this->createEventArgs();
        $this->assertEquals(null, $this->log->onDispatchLoopShutdown($args));
    }
}