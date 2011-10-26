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
class Enlight_Tests_Application_ConfigTest extends Enlight_Components_Test_TestCase
{
	/**
	 * Get options test case.
	 */
	public function testGetOptions()
    {
    	$app = Enlight_Application::Instance();

    	$this->assertNotEmpty($app->getOptions());
    }

	/**
	 * Set options test case.
	 */
	public function testSetOptions()
    {
    	$app = Enlight_Application::Instance();

		$options = $app->getOptions();
		$options['test'] = true;
		$app->setOptions($options);

		$this->assertTrue($app->getOption('test'));
    }

	/**
	 * Test case.
	 */
	public function testSetPhpSettings()
    {
    	$app = Enlight_Application::Instance();

		$options = $app->getOptions();
		$options['phpsettings']['session']['auto_start'] = 1;
		$app->setOptions($options);

		$this->assertNotEmpty(ini_get('session.auto_start'));
    }

	/**
	 * Test case.
	 */
	public function testSetIncludePaths()
    {
    	$app = Enlight_Application::Instance();

		$options = $app->getOptions();
		$options['includepaths'] = $app->Loader()->explodeIncludePath();
		$app->setOptions($options);

		$this->assertNotEmpty(get_include_path());
    }
}