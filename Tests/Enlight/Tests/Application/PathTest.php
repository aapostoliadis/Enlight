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
class Enlight_Tests_Application_PathTest extends Enlight_Components_Test_TestCase
{
	/**
	 * App path test case.
	 */
	public function testPaths()
    {
    	$app = Enlight_Application::Instance();

		$this->assertFileExists($app->Path());
    	$this->assertFileExists($app->AppPath());
    	$this->assertFileExists($app->ComponentsPath());
    	$this->assertFileExists($app->CorePath());
    }

	/**
	 * App path test case.
	 */
	public function testPathsExtends()
    {
    	$app = Enlight_Application::Instance();

		$this->assertFileExists($app->Path('Enlight'));
    	$this->assertFileExists($app->CorePath('Components'));
		$this->assertFileExists($app->ComponentsPath('Db'));
    }
}