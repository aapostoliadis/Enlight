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
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight_Cron
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */

class Enlight_Components_Cron_CronManager
{
	/**
	 * @var Enlight_Components_Cron_Adapter_Adapter
	 */
	private $_adapter = null;

	/**
	 * Constructor can be injected with a read / write adapter object
	 *
	 * @param Enlight_Components_Cron_Adapter_Adapter $adapter
	 */
	public function __construct(Enlight_Components_Cron_Adapter_Adapter $adapter)
	{
		$this->_adapter = $adapter;
	}

	/**
	 * Sets the read / write adapter
	 *
	 * @param Enlight_Components_Cron_Adapter_Adapter $adapter
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function setAdapter(Enlight_Components_Cron_Adapter_Adapter $adapter)
	{
		$this->_adapter = $adapter;
		return $this;
	}

	/**
	 * Returns the read / write adapter
	 *
	 * @return Enlight_Components_Cron_Adapter_Adapter|null
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}

	/**
	 * Deactivate a given Cron Job
	 *
	 * @throws Enlight_Exception
	 * @param Enlight_Components_Cron_CronArgs $jobArgs
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function removeJob(Enlight_Components_Cron_CronArgs $jobArgs)
	{
		$this->_adapter->removeJob($jobArgs->Job());
		return $this;
	}

	/**
	 * Updates a cron job
	 *
	 * @throws Enlight_Exception
	 * @param Enlight_Components_Cron_CronArgs $jobArgs
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function updateJob(Enlight_Components_Cron_CronArgs $jobArgs)
	{
		$this->_adapter->updateJob($jobArgs->Job());
		return $this;
	}

	/**
	 * Returns an array of Enlight_Components_Cron_Job from crontab
	 *
	 * @return array of Enlight_Components_Cron_CronArgs
	 */
	public function getAllJobs()
	{
		$jobs = $this->_adapter->getAllJobs();
		$retVal = array();
		foreach($jobs as $job)
		{
			$retVal[] = new Enlight_Components_Cron_CronArgs($job);
		}
		return	$retVal;
	}

	/**
	 * Receives a single Cron job defined by its id from crontab
	 *
	 * @param Int $id
	 * @return Enlight_Components_Cron_CronArgs
	 */
	public function getJobById($id)
	{
		$retVal = $this->_adapter->getJobById((int)$id);
		return new Enlight_Components_Cron_CronArgs($retVal);
	}

	/**
	 * Receives a single cron job by its name from the crontab
	 *
	 * @param String $name
	 * @return Enlight_Components_Cron_CronArgs
	 */
	public function getJobByName($name)
	{
		$retVal = $this->_adapter->getJobByName((string)$name);
		return new Enlight_Components_Cron_CronArgs($retVal);
	}

	/**
	 * Adds an job to the crontab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function addJob(Enlight_Components_Cron_Job $job)
	{
		$this->_adapter->addJob($job);
		return $this;
	}

	/**
	 * Removes an job from the crontab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function deleteJob(Enlight_Components_Cron_Job $job)
	{
		$this->_adapter->removeJob($job);
		return $this;
	}

	/**
	 * Returns the next cron job who is due to execute
	 *
	 * @return Enlight_Components_Cron_CronArgs
	 */
	public function getNextCronJob()
	{
		return new Enlight_Components_Cron_CronArgs( $this->_adapter->getNextJob());
	}


}
