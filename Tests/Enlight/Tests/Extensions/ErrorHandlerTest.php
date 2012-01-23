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
 * @covers     Enlight_Components_Site
 */
class Enlight_Tests_Extensions_ErrorHandler_BootstrapTest extends Enlight_Components_Test_Plugin_TestCase
{
    /**
     * @var Enlight_Extensions_ErrorHandler_Bootstrap
     */
    protected $errorHandler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $this->errorHandler = $this->getMock('Enlight_Extensions_ErrorHandler_Bootstrap', null, array(''));
        $namespace = new Enlight_Plugin_Namespace_Loader('Extensions');
        $namespace->registerPlugin($this->errorHandler);
        parent::setUp();
    }

    /**
     * test case
     */
    public function testInit()
    {
        $this->errorHandler->init();
        $list = $this->errorHandler->getErrorLevelList();

        if (defined('E_DEPRECATED')) {
            $this->assertArrayHasKey(E_DEPRECATED, $list);
        }
        if (defined('E_USER_DEPRECATED')) {
            $this->assertArrayHasKey(E_USER_DEPRECATED, $list);
        }

    }

    /**
     * test case
     */
    public function testInstall()
    {
        $errorHandler = $this->getMock(
            'Enlight_Extensions_ErrorHandler_Bootstrap', array('subscribeEvent'),
            array(), '', false
        );

        $errorHandler->expects($this->once())
                     ->method('subscribeEvent')
                     ->with($this->isType('string'));

        $this->assertTrue($errorHandler->install());
    }

    /**
     * test case
     */
    public function testStartDispatch()
    {
        $this->errorHandler->onStartDispatch();

        $this->assertTrue($this->errorHandler->isRegisteredErrorHandler());

        $this->assertArrayCount(2, $this->errorHandler->getOrigErrorHandler());
    }

    /**
     * test case
     */
    public function testRegisterErrorHandler()
    {
        $this->assertInstanceOf('Enlight_Extensions_ErrorHandler_Bootstrap', $this->errorHandler->registerErrorHandler(null));

        $this->assertInstanceOf('Enlight_Extensions_ErrorHandler_Bootstrap', $this->errorHandler->registerErrorHandler(E_ALL));

        $this->assertTrue($this->errorHandler->isRegisteredErrorHandler());

        $this->assertArrayCount(2, $this->errorHandler->getOrigErrorHandler());
    }

    /**
     * test case
     */
    public function testSetEnabledLog()
    {
        $this->errorHandler->setEnabledLog(true);
        $this->assertTrue($this->errorHandler->isEnabledLog());
    }

    /**
     * test case
     */
    public function testErrorHandler()
    {
        $this->errorHandler->setEnabledLog(true);

        $this->errorHandler->registerErrorHandler();

        $this->errorHandler->setOrigErrorHandler(array($this, "myOrigErrorHandler"));

        $this->errorHandler->errorHandler(1, "test", "test", 1, array("test"));

        $this->assertArrayCount(1, $this->errorHandler->getErrorLog());

    }



    public function myOrigErrorHandler()
    {
        $this->assertEquals(array(1, "test", "test", 1, array("test")), func_get_args());
    }






}
