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
 * Test case
 *
 * @category   Enlight
 * @package    Enlight_Tests
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Enlight_Tests_Application_BootstrapTest extends Enlight_Components_Test_TestCase
{
	/**
	 * Test case
	 */
	public function testCall()
    {
    	$app = Enlight_Application::Instance();

    	$this->assertInstanceOf('Enlight_View_ViewDefault', $app->View());
    }

	/**
	 * Test case
	 */
	public function testStaticCall()
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		    $this->assertInstanceOf('Enlight_View_ViewDefault', Enlight_Application::View());
        }
    }

	/**
	 * Test case
	 */
	public function testCallException()
    {
        $this->setExpectedException('Enlight_Exception');
    	$app = Enlight_Application::Instance();
		$app->Test();
    }

	/**
	 * Test case
	 */
	public function testStaticCallException()
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $this->setExpectedException('Enlight_Exception');
            Enlight_Application::Test();
        }
    }
}