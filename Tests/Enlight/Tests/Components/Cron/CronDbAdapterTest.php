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
	 * @var Enlight_Components_Cron_CronJob
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
		$dbFile = Enlight_TestHelper::Instance()->TestPath('TempFiles') . 'cron.db';

		if(file_exists($dbFile)) {
			unlink($dbFile);
		}

		$this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dbFile
        ));

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
		
        $this->object = new Enlight_Components_Cron_Adapter_DbAdapter($options);

		$jobData = array('id'=>'1',
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

		$this->job = new Enlight_Components_Cron_CronJob($jobData);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
		$dbFile = Enlight_TestHelper::Instance()->TestPath('TempFiles') . 'cron.db';
		unlink($dbFile);
		$this->object = null;
		$this->job = null;
    }

    /**
     * Tests if an array of options can be set
     */
    public function testSetOptions()
    {
        $options = array('id' => 'id_test',
						'name' => 'name_test',
						'action' => 'action_test',
						'data' => 'data_test',
						'next' => 'next_test',
						'start' => 'start_test',
						'interval' => 'interval_test',
						'active' => 'active_test',
						'end' => 'end_test',
						'crontab'	=> 's_crontab');
		
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->setOptions($options));

		foreach ($options as $key => $value) {
		    $this->assertEquals($value, $options[$key]);
		}
    }


    public function testGetAllCronJobs()
    {
		$this->assertArrayCount(0, $this->object->getAllCronJobs());
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->addCronJob($this->job));
		$this->assertArrayCount(1, $this->object->getAllCronJobs());
    }

    /**
     * Tests if a cron job can be deactivated
     */
    public function testDeactivateJob()
    {
		$job = new Enlight_Components_Cron_CronJob($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->addCronJob($job));
		$job = $this->object->getCronJobById(1);
		$this->assertTrue($job->isActive());
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->deactivateJob($job));
		$job = $this->object->getCronJobById(1);
		$this->assertFalse($job->isActive());
    }

    /**
     * Tests if an cron can be updated
     */
    public function testUpdateJob()
    {

		$job = new Enlight_Components_Cron_CronJob($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->addCronJob($job));

		$newOptions = array('name'=>'updatedName', 'interval'=>'80');
		$options = array_merge($this->jobData, $newOptions);

		$updatedJob = new Enlight_Components_Cron_CronJob($options);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->updateJob($updatedJob));
		$testData = $this->object->getCronJobById(1);
		$this->assertInstanceOf('Enlight_Components_Cron_CronJob', $testData);
		$this->assertEquals($newOptions['name'],$testData->getName());
		$this->assertEquals($newOptions['interval'], $testData->getInterval());
    }



    /**
     *
     */
    public function testGetCronJobById()
    {
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter',$this->object->addCronJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_CronJob',$this->object->getCronJobById(1));
    }

    /**
     *
     */
    public function testGetCronJobByName()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter',$this->object->addCronJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_CronJob',$this->object->getCronJobByName('Lagerbestand Warnung'));
    }

    public function testGetCronJobByAction()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter',$this->object->addCronJob($this->job));
        $this->assertInstanceOf('Enlight_Components_Cron_CronJob',$this->object->getCronJobByAction('article_stock'));
    }

    public function testAddCronJob()
    {
       $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter',$this->object->addCronJob($this->job));
    }


    public function testDeleteCronJob()
    {

		$job  = new Enlight_Components_Cron_CronJob($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->addCronJob($job));

		$this->jobData['action'] = 'test_action2';
		$this->jobData['id'] = 2;

		$job2  = new Enlight_Components_Cron_CronJob($this->jobData);
		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->object->addCronJob($job2));
		$this->assertArrayCount(2, $this->object->getAllCronJobs());

        $this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter',$this->object->deleteCronJob($this->job));
		$this->assertArrayCount(1, $this->object->getAllCronJobs());

		$this->assertNull($this->object->getCronJobById(1));
		$this->assertInstanceOf('Enlight_Components_Cron_CronJob', $job3=$this->object->getCronJobById(2));
		$this->assertEquals('test_action2', $job3->getAction());
    }

	public function testGetNextCronJob()
	{
		$this->jobData['next'] = date('Y-m-d H:i:s', time()-10);
		
		$job = new Enlight_Components_Cron_CronJob($this->jobData);

		$this->object->addCronJob($job);

		$nextJob = $this->object->getNextCronJob();
	}

}