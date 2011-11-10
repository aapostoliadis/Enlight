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
class Enlight_Tests_Components_Cron_SchedulerTest extends Enlight_Components_Test_TestCase
{
	/**
     * Set up the test case
     */
    public function setUp()
    {
        parent::setUp();
    }

	/**
	 * Tearing down
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	public function testNewScheduler()
	{
		$schedulerAdapter = new Enlight_Config_Adapter_File(
					array(	'configType'=>'ini',
							'configDir'=>'Enlight/Apps/Default/Crons/'));

		$schedulerData = new Enlight_Config('cron', array('adapter' => $schedulerAdapter, 'allowModifications' => false));
		$this->assertInstanceOf('Enlight_Components_Cron_Scheduler', new Enlight_Components_Cron_Scheduler($schedulerData)); 
	}
}
 
