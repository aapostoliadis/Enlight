<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Scheduler
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Scheduler 
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Cron_Scheduler implements Enlight_Components_Cron_CronScheduler
{

	/**
	 * Contains all known cron jobs
	 *
	 * @var array
	 */
	private $_cronJobs = array();

	/**
	 * Contains all currently running cron jobs
	 * @var array
	 */
	private $_cronJobsRunning = array();

	/**
	 * Stores the config adapter
	 * @var Enlight_Config
	 */
	private $_adapter;


	/**
	 * Constructor - needs an Enlight_Config object injected
	 *
	 * @param Enlight_Config $options
	 */
	public function __construct(Enlight_Config $options)
	{
		$this->_adapter = $options;
	}

	/**
	 * Returns an array of currently running cron jobs
	 *
	 * @return array
	 */
	public function getRunningJobs()
	{
		return $this->_cronJobsRunning;
	}

	/**
	 * Returns all known cron jobs
	 *
	 * @return array
	 */
	public function getJobs()
	{
		return $this->_cronJobs;
	}

	/**
	 * Marks the time the cron job started
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	function startCronJob(Enlight_Components_Cron_CronJob $job)
	{
		// TODO: Implement startCronJob() method.
	}

	/**
	 * Marks the time the cron job ended
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	function endCronJob(Enlight_Components_Cron_CronJob $job)
	{
		// TODO: Implement endCronJob() method.
	}

	/**
	 * Tries to execute the cron job
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	function runCronJob(Enlight_Components_Cron_CronJob $job)
	{
		// TODO: Implement runCronJob() method.
	}

	/**
	 * Runs all known cron jobs
	 *
	 * @return void
	 */
	function runCronJobs()
	{
		// TODO: Implement runCronJobs() method.
	}

	/**
	 * Marks a cron job as not running
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	function stopCronJob(Enlight_Components_Cron_CronJob $job)
	{
		// TODO: Implement stopCronJob() method.
	}

	/**
	 * Gets the next active cron job.
	 *
	 * @return mixed null on no cron job found
	 */
	function readCronJob()
	{
		// TODO: Implement readCronJob() method.
	}
}