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
class Enlight_Tests_Components_Cron_CronManager extends Enlight_Components_Test_TestCase
{
	/**
	 * @var Enlight_Components_Cron_Adapter_Adapter
	 */
	private $_adapter = null;

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $db;

	/**
	 * @var array
	 */
	private $options = array();

	/**
	 * @var Enlight_Components_Cron_CronManager
	 */
	private $manager;

	private $jobData = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
		$dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dir . 'cron.db'
        ));

		$dbFile = Enlight_TestHelper::Instance()->TestPath('TempFiles') . 'cron.db';
		if(file_exists($dbFile)) {
			unlink($dbFile);
		}

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

		$options = array('id' => 'id',
						'name' => 'name',
						'action' => 'action',
						'data' => 'data',
						'next' => 'next',
						'start' => 'start',
						'interval' => 'interval',
						'active' => 'active',
						'end' => 'end',
						'db'=>$this->db);

		$this->jobData =  array('id'=>'1',
						  'name'=>'Lagerbestand Warnung',
						  'action'=>'article_stock',
						  'data'=>'',
						  'next'=>'2010-10-16 12:34:33',
						  'start'=>'2010-10-16 12:34:31',
						  'interval'=>'5',
						  'active'=>'1',
						  'end'=>'2010-10-16 12:34:32',
						  'crontab'=>'s_crontab');
		$this->jobData['next'] = new Zend_Date($this->jobData['next']);
		$this->jobData['start'] = new Zend_Date($this->jobData['start']);
		$this->jobData['end']  = new Zend_Date($this->jobData['end']);
		$this->jobData['data'] = array('key'=>'value');
		
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->_adapter = new Enlight_Components_Cron_Adapter_DbAdapter($options));
		$this->assertInstanceOf('Enlight_Components_Cron_CronManager', $this->manager = new Enlight_Components_Cron_CronManager($this->_adapter));

		$job = new Enlight_Components_Cron_Job($this->jobData);

		$this->assertInstanceOf('Enlight_Components_Cron_CronManager',$this->manager->addJob($job));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		$dbFile = Enlight_TestHelper::Instance()->TestPath('TempFiles') . 'cron.db';
		unlink($dbFile);
    }

    /**
     * 
     */
    public function testSetAdapter()
    {
		$this->assertInstanceOf('Enlight_Components_Cron_CronManager', $this->manager->setAdapter($this->_adapter));
    }

    /**
     * @todo Implement testGetAdapter().
     */
    public function testGetAdapter()
    {
       $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->manager->getAdapter());
    }

    public function testDeactivateJob()
    {
        $jobArgs = $this->manager->getJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_CronArgs', $jobArgs);
		$this->assertTrue($jobArgs->Job()->isActive());

		$this->manager->removeJob($jobArgs);
		$jobAfter = $this->manager->getJobById(1);
		$this->assertNull($jobAfter);
    }

    /**
     * @todo Implement testUpdateJob().
     */
    public function testUpdateJob()
    {
        $cronArgs = $this->manager->getJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_CronArgs', $cronArgs);

		$cronArgs->Job()->setName('test me');

		$this->manager->updateJob($cronArgs);

		$jobAfter = $this->manager->getJobById($cronArgs->Job()->getId());
		$this->assertEquals('test me', $jobAfter->Job()->getName());

    }

    /**
     * @todo Implement testGetAllJobs().
     */
    public function testGetAllJobs()
    {
        $this->assertArrayCount(1,$this->manager->getAllJobs());
    }

    /**
     * @todo Implement testGetJobById().
     */
    public function testGetJobById()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_CronArgs', $this->manager->getJobById(1));
    }

    /**
     * @todo Implement testGetJobByName().
     */
    public function testGetJobByName()
    {
        // Remove the following lines when you implement this test.
        $this->assertInstanceOf('Enlight_Components_Cron_CronArgs', $this->manager->getJobByName('Lagerbestand Warnung'));
    }

    /**
     * @todo Implement testAddJob().
     */
    public function testAddJob()
    {
		$this->assertArrayCount(1,$this->manager->getAllJobs());
		$this->jobData['action'] = $this->jobData['action']."2";
		$this->jobData['id'] = 2;
		$job = new Enlight_Components_Cron_Job($this->jobData);
		$this->manager->addJob($job);
		$this->assertArrayCount(2,$this->manager->getAllJobs());
    }
}

