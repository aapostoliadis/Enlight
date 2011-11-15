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
 * @version    $Id:$
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
	private $jobData;
	private $db;
	private $options;
	/**
	 * @var Enlight_Components_Cron_CronManager
	 */
	private $manager;
	/**
	 * @var Enlight_Components_Cron_Adapter_DbAdapter
	 */
	private $_adapter;
	/**
     * Set up the test case
     */
    public function setUp()
    {
		$dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dir . 'cron.db'
        ));
		
		$dbFile = Enlight_TestHelper::Instance()->TestPath('TempFiles') . 'cron.db';
		if(file_exists($dbFile))
			unlink($dbFile);

		$sql = '
				CREATE TABLE IF NOT EXISTS `s_crontab` (
				  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				  `name` varchar(255) NOT NULL,
				  `action` varchar(255) NOT NULL,
				  `data` text NOT NULL,
				  `next` datetime DEFAULT NULL,
				  `start` datetime DEFAULT NULL,
				  `interval` int(11) NOT NULL,
				  `active` int(1) NOT NULL,
				  `end` datetime DEFAULT NULL,
				  UNIQUE (`action`)
				);
		';

		$this->db->exec($sql);

		$this->options =  array('id' => 'id',
						'name' => 'name',
						'action' => 'action',
						'data' => 'data',
						'next' => 'next',
						'start' => 'start',
						'interval' => 'interval',
						'active' => 'active',
						'end' => 'end',
						'db'=>$this->db);
		
		$this->jobData = array('id'=>'1',
						  'name'=>'Lagerbestand Warnung',
						  'action'=>'article_stock',
						  'data'=>'',
						  'next'=>'2010-10-16 12:34:33',
						  'start'=>'2010-10-16 12:34:31',
						  'interval'=>'5',
						  'active'=>'1',
						  'end'=>'2010-10-16 12:34:32',
						  'crontab'=>'s_crontab');

		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->_adapter = new Enlight_Components_Cron_Adapter_DbAdapter($this->options));
		$this->assertInstanceOf('Enlight_Components_Cron_CronManager', $this->manager = new Enlight_Components_Cron_CronManager($this->_adapter));

		$job = new Enlight_Components_Cron_CronJob($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_CronManager',$this->manager->addCronJob($job));
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

	/**
     * @todo Implement testStartCronJob().
     */
    public function testStartCronJob()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testEndCronJob().
     */
    public function testEndCronJob()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRunCronJobs().
     */
    public function testRunCronJobs()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRunCronJob().
     */
    public function testRunCronJob()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testStopCronJob().
     */
    public function testStopCronJob()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testReadCronJob().
     */
    public function testReadCronJob()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
 
