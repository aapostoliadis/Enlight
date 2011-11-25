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
	 * @var Enlight_Components_Cron_Adapter
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
	 * @var Enlight_Components_Cron_Manager
	 */
	private $manager;

	private $jobData = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array('dbname' => ':memory:'));

		$sql = '
				CREATE TABLE IF NOT EXISTS `test_crontab` (
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

		$options = array('idColumn' => 'id',
						'nameColumn' => 'name',
						'actionColumn' => 'action',
						'dataColumn' => 'data',
						'nextColumn' => 'next',
						'startColumn' => 'start',
						'intervalColumn' => 'interval',
						'activeColumn' => 'active',
						'endColumn' => 'end',
						'name' => 'test_crontab',
						'db'=>$this->db);

		$this->jobData =  array(
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
		
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->_adapter = new Enlight_Components_Cron_Adapter_DbTable($options));
		$this->assertInstanceOf('Enlight_Components_Cron_Manager', $this->manager = new Enlight_Components_Cron_Manager($this->_adapter));

		$job = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Manager',$this->manager->addJob($job));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		$this->db->exec('DROP TABLE IF EXISTS s_crontab ');
	}

    /**
     *
     */
    public function testSetAdapter()
    {
		$this->assertInstanceOf('Enlight_Components_Cron_Manager', $this->manager->setAdapter($this->_adapter));
    }

    public function testGetAdapter()
    {
       $this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->manager->getAdapter());
    }

    public function testDeactivateJob()
    {
        $job = $this->manager->getJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $job);
		$this->assertTrue($job->isActive());

		$this->manager->removeJob($job);
		$jobAfter = $this->manager->getJobById(1);
		$this->assertNull($jobAfter);
    }

    public function testUpdateJob()
    {
        $job = $this->manager->getJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $job);
		$job->setName('test me');
		$this->manager->updateJob($job);
		$jobAfter = $this->manager->getJobById($job->getId());
		$this->assertEquals('test me', $jobAfter->getName());

    }

    public function testGetAllJobs()
    {
        $this->assertArrayCount(1,$this->manager->getAllJobs());
    }

    public function testGetJobById()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->manager->getJobById(1));
    }

    public function testGetJobByName()
    {
        // Remove the following lines when you implement this test.
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->manager->getJobByName('Lagerbestand Warnung'));
    }

    public function testAddJob()
    {
		$this->assertArrayCount(1,$this->manager->getAllJobs());
		$this->jobData['action'] = $this->jobData['action']."2";

		$job = new Enlight_Components_Cron_Job($this->jobData);

		$this->manager->addJob($job);
		$this->assertArrayCount(2,$this->manager->getAllJobs());
    }

	public function testGetEventManager()
	{
		$this->assertInstanceOf('Enlight_Event_EventManager', $this->manager->getEventManager());
	}
	public function testDisableJob()
	{
		$job = $this->manager->getJobById(1);
		$this->assertTrue($job->isActive());
		$this->manager->disableJob($job);
		$this->assertFalse($job->isActive());
	}

	public function testGetNextJob()
	{
		$zendDate = new Zend_Date();
		$zendDate->subSecond(10);
		$this->jobData['next'] = $zendDate;

		unset($this->jobData['id']);
		$this->jobData['action'] = 'action_test';
		$job = new Enlight_Components_Cron_Job($this->jobData);

		$this->manager->addJob($job);
		$nextJob = $this->manager->getNextJob();
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $nextJob);
	}

	public function testRun()
	{
		$job = $this->manager->getJobById(1);
		$this->assertNull($this->manager->run($job));

		$handler = new Enlight_Event_Handler_Default(
							$job->getAction(), null, array($this, 'onJobAction')
		);

		$this->manager->getEventManager()->registerListener($handler);

		$this->manager->run($job);
	}
	public function testRunFailed()
	{
		$this->setExpectedException('Enlight_Exception');
		$job = $this->manager->getJobById(1);
		$this->assertNull($this->manager->run($job));

		$handler = new Enlight_Event_Handler_Default(
							$job->getAction(), null, array($this, 'onJobAction2')
		);

		$this->manager->getEventManager()->registerListener($handler);

		$this->manager->run($job);
	}
	/////////////// those methods below  will be called from the event   ////////////////////////
	public function onJobAction(Enlight_Components_Cron_EventArgs $args)
	{
		$this->assertEquals('article_stock', $args->Job()->getAction());
	}

	public function onJobAction2(Enlight_Components_Cron_EventArgs $args)
	{
		throw new Enlight_Exception('Failed on purpose.');
	}

}

