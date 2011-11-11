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
	/**
     * Set up the test case
     */
    public function setUp()
    {
		$dir = Enlight_TestHelper::Instance()->TestPath('TempFiles');
        $this->db = Enlight_Components_Db::factory('PDO_SQLITE', array(
            'dbname'   => $dir . 'cron.db'
        ));

		$sql = '
				CREATE TABLE IF NOT EXISTS `s_crontab` (
				  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
				  `name` varchar(255) NOT NULL,
				  `action` varchar(255) NOT NULL,
				  `elementID` int(11) DEFAULT NULL,
				  `data` text NOT NULL,
				  `next` datetime DEFAULT NULL,
				  `start` datetime DEFAULT NULL,
				  `interval` int(11) NOT NULL,
				  `active` int(1) NOT NULL,
				  `end` datetime DEFAULT NULL,
				  `inform_template` varchar(255) NOT NULL,
				  `inform_mail` varchar(255) NOT NULL,
				  `pluginID` int(11) DEFAULT NULL,
				  UNIQUE (`action`)
				);
		';

		$this->db->exec($sql);

		$this->options = array('id' => 'id',
						'name' => 'name',
						'action' => 'action',
						'elementID' => 'elementID',
						'data' => 'data',
						'next' => 'next',
						'start' => 'start',
						'interval' => 'interval',
						'active' => 'active',
						'end' => 'end',
						'inform_template' => 'inform_template',
						'inform_mail' => 'inform_mail',
						'db'=>$this->db,
						'crontab' => 's_crontab',
						'pluginID' => 'pluginID');
		$this->jobData =  array('id'=>'1',
						  'name'=>'Lagerbestand Warnung',
						  'action'=>'article_stock',
						  'elementID'=>NULL,
						  'data'=>'a:2:{s:5:"count";i:64;s:17:"articledetailsIDs";a:64:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:8;i:6;i:9;i:7;i:10;i:8;i:14;i:9;i:16;i:10;i:17;i:11;i:19;i:12;i:20;i:13;i:42;i:14;i:51;i:15;i:52;i:16;i:53;i:17;i:55;i:18;i:73;i:19;i:76;i:20;i:106;i:21;i:107;i:22;i:121;i:23;i:141;i:24;i:152;i:25;i:153;i:26;i:157;i:27;i:250;i:28;i:450;i:29;i:538;i:30;i:687;i:31;i:708;i:32;i:1049;i:33;i:1142;i:34;i:1151;i:35;i:1185;i:36;i:1186;i:37;i:1247;i:38;i:1395;i:39;i:1413;i:40;i:1420;i:41;i:1594;i:42;i:1648;i:43;i:1977;i:44;i:2194;i:45;i:2285;i:46;i:2374;i:47;i:2647;i:48;i:2662;i:49;i:2682;i:50;i:2732;i:51;i:2888;i:52;i:2889;i:53;i:2892;i:54;i:2928;i:55;i:3598;i:56;i:3624;i:57;i:3663;i:58;i:3827;i:59;i:3859;i:60;i:5957;i:61;i:6199;i:62;i:6596;i:63;i:6664;}}',
						  'next'=>'2010-10-16 12:34:33',
						  'start'=>'2010-10-16 12:34:31',
						  'interval'=>'5',
						  'active'=>'1',
						  'end'=>'2010-10-16 12:34:32',
						  'inform_template'=>'sARTICLESTOCK',
						  'inform_mail'=>'{$sConfig.sMAIL}',
						  'pluginID'=>NULL);

		$this->assertInstanceOf('Enlight_Components_Cron_Adapter_Adapter', $this->_adapter = new Enlight_Components_Cron_Adapter_DbAdapter($this->options));
		$this->assertInstanceOf('Enlight_Components_Cron_CronManager', $this->manager = new Enlight_Components_Cron_CronManager($this->_adapter));

		$name = Enlight_Components_Cron_CronManager::getCronAction($this->jobData['action']);
		$job = new Enlight_Components_Cron_CronJob($name, $this->jobData);
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

	public function testNewScheduler()
	{

	}
}
 
