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
class Enlight_Tests_Components_Cron_CronDbAdapterTest extends Enlight_Components_Test_TestCase
{
 /**
     * @var Enlight_Components_Cron_Adapter_DbAdapter
     */
    protected $object;

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $db;

	/**
	 * @var Enlight_Components_Cron_Job
	 */
	protected $job;

	/**
	 * @var array
	 */
	protected $jobData = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
		$this->db = Enlight_Components_Db::factory('PDO_SQLITE', array('dbname' => Enlight_TestHelper::Instance()->TestPath('TempFiles').'cron.db'));
		$sql = ' DROP TABLE IF EXISTS `test_crontab`;
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
		
        $this->object = new Enlight_Components_Cron_Adapter_DbTable($options);

		$jobData = array(
						  'name'=>'Lagerbestand Warnung',
						  'action'=>'article_stock',
						  'data'=>'',
						  'next'=>'2010-10-16 12:34:33',
						  'start'=>'2010-10-16 12:34:31',
						  'interval'=>'5',
						  'active'=>'1',
						  'end'=>'2010-10-16 12:34:32',
						  'crontab'=>'s_crontab');

		$this->jobData = $jobData;

		$this->jobData['next'] = new Zend_Date($this->jobData['next']);
		$this->jobData['start'] = new Zend_Date($this->jobData['start']);
		$this->jobData['end'] = new Zend_Date($this->jobData['end']);

		$this->job = new Enlight_Components_Cron_Job($this->jobData);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		$this->object = null;
		$this->job = null;
		$this->db->exec('DROP TABLE IF EXISTS s_crontab ');
    }

    /**
     * Tests if an array of options can be set
     */
    public function testSetOptions()
    {
		$options = array('idColumn' => 'id',
						'nameColumn' => 'name',
						'actionColumn' => 'action',
						'dataColumn' => 'data',
						'nextColumn' => 'next',
						'startColumn' => 'start',
						'intervalColumn' => 'interval',
						'activeColumn' => 'active',
						'endColumn' => 'end',
						'db'=>$this->db);
		
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->setOptions($options));

		foreach ($options as $key => $value) {
		    $this->assertEquals($value, $options[$key]);
		}
    }


    public function testGetAllCronJobs()
    {
		$this->assertArrayCount(0, $f=$this->object->getAllJobs());
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($this->job));
		$this->assertArrayCount(1, $this->object->getAllJobs());
    }

	public function testGetAllCronJobsIgnoreActive()
    {
		$this->assertArrayCount(0, $this->object->getAllJobs());
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($this->job));
		$this->job->setAction('foo');
		$this->job->setActive(false);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($this->job));
		$this->assertArrayCount(1, $this->object->getAllJobs());
		$this->assertArrayCount(2, $this->object->getAllJobs(true));
    }

    /**
     * Tests if an cron can be updated
     */
    public function testUpdateJob()
    {
		$job = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($job));

		$newOptions = array('name'=>'updatedName', 
							'interval'=>'80', 
							'id' =>1);
		$options = array_merge($this->jobData, $newOptions);

		$updatedJob = new Enlight_Components_Cron_Job($options);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->updateJob($updatedJob));
		$testData = $this->object->getJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $testData);
		
		$this->assertEquals($newOptions['name'],$testData->getName());
		$this->assertEquals($newOptions['interval'], $testData->getInterval());
    }

	public function testUpdateJobFail()
    {
		$this->setExpectedException('Enlight_Exception');
		$job = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($job));

		$newOptions = array('name'=>'updatedName',
							'interval'=>'80');
		$options = array_merge($this->jobData, $newOptions);

		$updatedJob = new Enlight_Components_Cron_Job($options);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->updateJob($updatedJob));
    }

    /**
     *
     */
    public function testGetCronJobById()
    {
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->addJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_Job',$job = $this->object->getJobById(1));
		$this->assertEquals($this->jobData['name'], $job->getName());
    }

    /**
     *
     */
    public function testGetCronJobByName()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->addJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_Job',$job = $this->object->getJobByName('Lagerbestand Warnung'));
		$this->assertEquals($this->jobData['action'], $job->getAction());
    }

    public function testGetCronJobByAction()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->addJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $job = $this->object->getJobByAction('article_stock'));
		$this->assertEquals($this->jobData['name'], $job->getName());
    }

    public function testAddCronJob()
    {
		unset($this->jobData['next']);
		unset($this->jobData['start']);
		unset($this->jobData['end']);
		$this->jobData['action'] = 'foo';

		$job = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->addJob($job));
    }
	 public function testAddCronJobFail()
    {
		$this->setExpectedException('Exception');
		$this->job->setId(1);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->addJob($this->job));
    }

    public function testDeleteCronJob()
    {

		$job  = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($job));
		$this->job->setId(1);

		$this->jobData['action'] = 'test_action2';

		unset($this->jobData['id']);
		$job2  = new Enlight_Components_Cron_Job($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter', $this->object->addJob($job2));
		$this->assertArrayCount(2, $f= $this->object->getAllJobs());

        $this->assertInstanceOf('Enlight_Components_Cron_Adapter',$this->object->removeJob($this->job));

		$this->assertNull($this->object->getJobById(1));
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $job3=$this->object->getJobById(2));
		$this->assertEquals('test_action2', $job3->getAction());
    }

	public function testGetNextCronJob()
	{
		$zendDate = new Zend_Date();
		$zendDate->subSecond(10);
		$this->jobData['next'] = $zendDate;

		$job = new Enlight_Components_Cron_Job($this->jobData);

		$this->object->addJob($job);

		$nextJob = $this->object->getNextJob();
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $nextJob);
	}

}