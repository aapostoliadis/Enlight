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
 * @covers     Enlight_Extensions_Debug_Bootstrap
 */
class Enlight_Tests_Extensions_Debug_BootstrapTest extends Enlight_Components_Test_Plugin_TestCase
{
    /**
     * @var Enlight_Extensions_Debug_Bootstrap
     */
    protected $debug;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->debug = $this->getMock('Enlight_Extensions_Debug_Bootstrap', null, array('Debug'));
        $namespace = new Enlight_Plugin_Namespace_Loader('Extensions');
        $namespace->registerPlugin($this->debug);
        parent::setUp();
    }

    /**
     * Test case
     */
    public function testInstall()
    {
        $debug = $this->getMock(
            'Enlight_Extensions_Debug_Bootstrap', array('subscribeEvent'),
            array(), '', false
        );
        $debug->expects($this->any())
            ->method('subscribeEvent')
            ->with($this->isType('string'), $this->isType('string'), $this->anything());
        $this->assertTrue($debug->install());
    }

    /**
     * Test case
     */
    public function testOnStartDispatch()
    {
        $namespace = new Enlight_Plugin_Namespace_Loader('Extensions');
        $debug = new Enlight_Extensions_Debug_Bootstrap('debug');
        $namespace->registerPlugin($debug);
        $errorHandler = $this->getMock(
            'Enlight_Extensions_ErrorHandler_Bootstrap',
            array('setEnabledLog', 'registerErrorHandler'),
            array('ErrorHandler')
        );
        $namespace->registerPlugin($errorHandler);

        $errorHandler->expects($this->once())
            ->method('setEnabledLog')
            ->with($this->isType('bool'));
        $errorHandler->expects($this->once())
            ->method('registerErrorHandler')
            ->with($this->isType('int'));

        $this->assertEquals(null, $debug->onStartDispatch($this->createEventArgs()));
    }

    /**
     * Test case
     */
    public function testOnAfterRenderView()
    {
        /** @var $log Enlight_Components_Log */
        $log = $this->getMock(
            'Enlight_Components_Log',
            array('table')
        );
        $log->expects($this->exactly(2))
            ->method('table')
            ->with($this->isType('array'));

        $this->debug->setLog($log);

        /** @var $template Enlight_Template_Default */
        $template = $this->getMock(
            'Enlight_Template_Default',
            array(),
            array(),
            '',
            false
        );
        $template->expects($this->once())
           ->method('getTemplateVars')
           ->will($this->returnValue(array('key' => 'value')));
        $template->expects($this->once())
           ->method('getConfigVars')
           ->will($this->returnValue(array('key' => 'value')));

        $eventArgs = $this->createEventArgs(array(
            'template' => $template
        ));
        $this->assertEquals(null, $this->debug->onAfterRenderView($eventArgs));
    }

    /**
     * Test case
     */
    public function testOnDispatchLoopShutdown()
    {
        $debug = new Enlight_Extensions_Debug_Bootstrap('Debug');

        $errorHandler = $this->getMock(
            'Enlight_Extensions_ErrorHandler_Bootstrap',
            array('getErrorLog'),
            array('ErrorHandler')
        );
        $errorHandler->expects($this->once())
            ->method('getErrorLog')
            ->will($this->returnValue(array(array(
                'count'   => 1,
                'code'    => E_ERROR,
                'name'    => 'test',
                'message' => 'test',
                'line'    => 1,
                'file'    => __FILE__
            ))));

        $namespace = new Enlight_Plugin_Namespace_Loader('TestDebug');
        $namespace->registerPlugin($errorHandler);
        $namespace->registerPlugin($debug);

        $log = $this->getMock(
            'Enlight_Components_Log',
            array('table', 'err')
        );
        $log->expects($this->exactly(2))
            ->method('table')
            ->with($this->isType('array'));
        $log->expects($this->once())
            ->method('err')
            ->with($this->isType('string'));
        $debug->setLog($log);

        $response = $this->Response();
        $response->setException(new Enlight_Exception('test', 123));
        $front = $this->Front()->setResponse($response);
        $eventArgs = $this->createEventArgs(array('subject' => $front));

        $this->assertEquals(null, $debug->onDispatchLoopShutdown($eventArgs));
    }

    /**
     * Test case
     */
    public function helpTestLogError()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * Test case
     */
    public function helpTestLogTemplate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * Test case
     */
    public function helpTestLogException()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * Test case
     */
    public function testEncode()
    {
        $result = $this->debug->encode(new Enlight_Config(array('test' => 1)));
        $this->assertInternalType('array', $result);
        $result = $this->debug->encode(new ArrayObject(array('test' => 1)));
        $this->assertInternalType('array', $result);
        $result = $this->debug->encode(str_repeat('test', 100));
        $this->assertLessThan(300, strlen($result));
        $result = $this->debug->encode(new Exception('test'));
        $this->assertEquals('Exception', $result);
    }
}