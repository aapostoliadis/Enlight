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
class Enlight_Components_Cron_Scheduler //implements Enlight_Components_Cron_CronScheduler
{
	/**
	 * Stores the config adapter
	 * @var Enlight_Components_Cron_CronManager
	 */
	private $_cronManager;

	/**
	 * @var Enlight_Event_EventManager
	 */
	private $_eventManager;

	/**
	 * @var Enlight_Event_EventArgs
	 */
	private $_eventArgs;

	/**
	 * Constructor - needs an Enlight_Config object injected and
	 * a event Manager. If no EventManger provided we try to get
	 * the system event manager.
	 *
	 * @param Enlight_Components_Cron_Adapter_Adapter $adapter
	 * @param null $eventManager
	 *
	 * @param null $eventArgs
	 * @return Enlight_Components_Cron_Scheduler
	 */
	public function __construct(Enlight_Components_Cron_Adapter_Adapter $adapter, $eventManager = null, $eventArgs = null)
	{
		$this->_cronManager = new Enlight_Components_Cron_CronManager($adapter);

		if(!is_null($eventManager) && ($eventManager instanceof Enlight_Event_EventManager)){
			$this->_eventManager = $eventManager;
		}
		else {
			$this->_eventManager = Enlight_Application::Instance()->Events();
		}

	}


	/**
	 * Marks the time the cron job started
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return boolean
	 */
	function startCronJob(Enlight_Components_Cron_Job $job)
	{
		// Move next date to previous date
		//$job->previous = $job->next;
		
		//$job->next = strtotime($job->next);
		// $job->next gets transformed into a unix timestamp
		$nextTimestamp = strtotime($job->getNext());

		// re-scheduler loop
		// updates cron job to the next runtime in the future
		// sets $job->next to a new runtime by adding $job->interval to it
		do {
			$nextTimestamp += $job->getInterval();
		} while($nextTimestamp < time() );

		// convert the current time to a MySQL datetime format
		$job->setStart(date('Y-m-d H:i:s', time()));
		$job->setEnd(NULL);

		// Save the job but don't save the next run time yet - this info has to be written when the Cron ended.
		try {
			$this->_cronManager->updateJob($job);
			$job->setNext(date('Y-m-d H:i:s', $nextTimestamp));
			return true;
		}
		catch(Exception $e) {
			return false;
		}
	}

	/**
	 * Marks the time the cron job ended and updates the next datetime
	 * field with the next date this cron is scheduled to run
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return void
	 */
	function endCronJob(Enlight_Components_Cron_Job $job)
	{
		$job->setEnd(date('Y-m-d H:i:s', time()));
		//$job->setData($job->data);
		//$job->next = $job->next;
		$this->_cronManager->updateJob($job);
	}

	/**
	 * Tries to execute cron job which are due
	 *
	 * @internal param \Enlight_Components_Cron_Job $job
	 * @return void
	 */
	function runCronJobs()
	{
		while($job = $this->readCronJob()) {
			$this->runCronJob($job);
		}
	}

	/**
	 * Runs all known cron jobs
	 *
	 * @param \Enlight_Components_Cron_Job $job
	 * @return void
	 */
	function runCronJob(Enlight_Components_Cron_Job $job)
	{
		//$eventArgs = new Enlight_Event_EventArgs($job->getAction(), $job->getData());
		$eventArgs = new Enlight_Components_Cron_EventArgs($job);
		try {
			if($this->startCronJob($job)) {
				$this->_eventManager->notifyUntil($eventArgs);
				$this->endCronJob($job);
			}
		}
		catch(Exception $e) {
			$job->setData((array('error'=>$e->getMessage())));
			$this->stopCronJob($job);
		}
	}

	/**
	 * Marks a cron job as not running
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return void
	 */
	function stopCronJob(Enlight_Components_Cron_Job $job)
	{
		$this->_cronManager->updateJob($job);
		$this->_cronManager->removeJob($job);
	}

	/**
	 * Gets the next active cron job.
	 *
	 * @return mixed null on no cron job found
	 */
	function readCronJob()
	{
		return $this->_cronManager->getNextCronJob();
	}
}
