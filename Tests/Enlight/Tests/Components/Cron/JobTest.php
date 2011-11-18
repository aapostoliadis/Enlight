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
class Enlight_Tests_Components_Cron_Cronjob extends Enlight_Components_Test_TestCase
{
	private $jobData = array('id'=>'1',
						  'name'=>'Lagerbestand Warnung',
						  'action'=>'article_stock',
						  'data'=>'',
						  'next'=>'2010-10-16 12:34:33',
						  'start'=>'2010-10-16 12:34:31',
						  'interval'=>'5',
						  'active'=>'1',
						  'end'=>'2010-10-16 12:34:32',
						  'crontab'=>'s_crontab');
	/**
	 * @var Enlight_Components_Cron_Job
	 */
	private $job;

	/**
     * Set up the test case
     */
    public function setUp()
    {
		$this->jobData['next'] = new Zend_Date($this->jobData['next']);
		$this->jobData['start'] = new Zend_Date($this->jobData['start']);
		$this->jobData['end'] = new Zend_Date($this->jobData['end']);
		$this->job = new Enlight_Components_Cron_Job($this->jobData);
		$this->job->setData(unserialize('a:2:{s:5:"count";i:64;s:17:"articledetailsIDs";a:64:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:8;i:6;i:9;i:7;i:10;i:8;i:14;i:9;i:16;i:10;i:17;i:11;i:19;i:12;i:20;i:13;i:42;i:14;i:51;i:15;i:52;i:16;i:53;i:17;i:55;i:18;i:73;i:19;i:76;i:20;i:106;i:21;i:107;i:22;i:121;i:23;i:141;i:24;i:152;i:25;i:153;i:26;i:157;i:27;i:250;i:28;i:450;i:29;i:538;i:30;i:687;i:31;i:708;i:32;i:1049;i:33;i:1142;i:34;i:1151;i:35;i:1185;i:36;i:1186;i:37;i:1247;i:38;i:1395;i:39;i:1413;i:40;i:1420;i:41;i:1594;i:42;i:1648;i:43;i:1977;i:44;i:2194;i:45;i:2285;i:46;i:2374;i:47;i:2647;i:48;i:2662;i:49;i:2682;i:50;i:2732;i:51;i:2888;i:52;i:2889;i:53;i:2892;i:54;i:2928;i:55;i:3598;i:56;i:3624;i:57;i:3663;i:58;i:3827;i:59;i:3859;i:60;i:5957;i:61;i:6199;i:62;i:6596;i:63;i:6664;}}'));
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


	public function testCreateCronJob()
	{
		$this->assertInstanceOf('Enlight_Components_Cron_Job', $job = new Enlight_Components_Cron_Job($this->jobData));
	}

	 /**
     *
     */
    public function testSetOptions()
    {
        $testName = "Test Name";
		$job = $this->job;
		$job->setOptions(array('name'=>$testName));
		$this->assertEquals($testName, $job->getName());
		$this->assertNotEquals($this->jobData['name'], $job->getName());
		$this->assertEquals($this->jobData['action'], $job->getAction());
    }

    /**
     *
     */
    public function testSetData()
    {
        $data = "A String";
		$this->assertArrayCount(2, unserialize($this->job->getData()));
		$this->assertInstanceOf('Enlight_Components_Cron_Job',$this->job->setData($data));
		$this->assertEquals($data, $this->job->getData());
    }

    /**
     *
     */
    public function testGetData()
    {
        $data = "A String";
		$this->assertArrayCount(2, unserialize($this->job->getData()));
		$this->assertInstanceOf('Enlight_Components_Cron_Job',$this->job->setData($data));
		$this->assertEquals($data, $this->job->getData());
    }


    /**
     *
     */
    public function testGetId()
    {
        $this->assertEquals(1, $this->job->getId());
    }

    /**
     *
     */
    public function testSetId()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setId('2'));
		$this->assertEquals('2', $this->job->getId());
    }

    /**
     *
     */
    public function testGetName()
    {
        $this->assertEquals($this->jobData['name'], $this->job->getName());
    }

    /**
     *
     */
    public function testSetName()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setName('test name'));
		$this->assertEquals('test name', $this->job->getName());
    }

    /**
     * @todo Implement testGetAction().
     */
    public function testGetAction()
    {
        $this->assertEquals($this->jobData['action'], $this->job->getAction());
    }

    /**
     * @todo Implement testSetAction().
     */
    public function testSetAction()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setAction('test_action'));
		$this->assertEquals('test_action', $this->job->getAction());
    }

    /**
     * @todo Implement testGetNext().
     */
    public function testGetNext()
    {
       $this->assertEquals($this->jobData['next'], $this->job->getNext());
    }

    /**
     * @todo Implement testSetNext().
     */
    public function testSetNext()
    {
		$ts = new Zend_Date();
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setNext($ts));
		$this->assertEquals($ts, $this->job->getNext());
    }

    /**
     * @todo Implement testGetStart().
     */
    public function testGetStart()
    {
		$this->assertInstanceOf('Zend_Date', $this->job->getStart());
		$this->assertEquals($this->jobData['start'], $this->job->getStart());
    }

    /**
     * @todo Implement testSetStart().
     */
    public function testSetStart()
    {
		$ts = new Zend_Date();
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setStart($ts));
		$this->assertEquals($ts, $this->job->getStart());
    }

    /**
     * @todo Implement testGetEnd().
     */
    public function testGetEnd()
    {
        $this->assertEquals($this->jobData['end'], $this->job->getEnd());
    }

    /**
     * @todo Implement testSetEnd().
     */
    public function testSetEnd()
    {
		$ts = new Zend_Date();
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setStart($ts));
		$this->assertEquals($ts, $this->job->getStart());
    }

    /**
     * @todo Implement testGetInterval().
     */
    public function testGetInterval()
    {
        $this->assertEquals($this->jobData['interval'], $this->job->getInterval());
    }

    /**
     * @todo Implement testSetInterval().
     */
    public function testSetInterval()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setInterval('8000'));
		$this->assertEquals(8000, $this->job->getInterval());
    }

    /**
     * @todo Implement testGetActive().
     */
    public function testIsActive()
    {
        $this->assertTrue($this->job->isActive());
    }

    /**
     * @todo Implement testSetActive().
     */
    public function testSetActive()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setActive(false));
		$this->assertFalse($this->job->isActive());
    }

    /**
     * @todo Implement testGetCrontab().
     */
    public function testGetCrontab()
    {
        $this->assertEquals($this->jobData['crontab'], $this->job->getCrontab());
    }

    /**
     * @todo Implement testSetCrontab().
     */
    public function testSetCrontab()
    {
        $this->assertInstanceOf('Enlight_Components_Cron_Job', $this->job->setCrontab('test_crontab'));
		$this->assertEquals('test_crontab', $this->job->getCrontab());
    }
}


