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
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function deactivateJob(Enlight_Components_Cron_CronJob $job)
	{
		$this->_adapter->deactivateJob($job);
		return $this;
	}

	/**
	 * Updates a cron job
	 *
	 * @throws Enlight_Exception
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function updateJob(Enlight_Components_Cron_CronJob $job)
	{
		$this->_adapter->updateJob($job);
		return $this;
	}

	/**
	 * Returns an array of Enlight_Components_Cron_CronJob from crontab
	 *
	 * @return array
	 */
	public function getAllCronJobs()
	{
		$retVal = $this->_adapter->getAllCronJobs();
		return	$retVal;
	}

	/**
	 * Receives a single Cron job defined by its id from crontab
	 *
	 * @param Int $id
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function getCronJobById($id)
	{
		$retVal = $this->_adapter->getCronJobById((int)$id);
		return $retVal;
	}

	/**
	 * Receives a single cron job by its name from the crontab
	 *
	 * @param String $name
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function getCronJobByName($name)
	{
		$retVal = $this->_adapter->getCronJobByName((string)$name);
		return $retVal;
	}

	/**
	 * Adds an job to the crontab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function addCronJob(Enlight_Components_Cron_CronJob $job)
	{
		$this->_adapter->addCronJob($job);
		return $this;
	}

	/**
	 * Removes an job from the crontab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function deleteCronJob(Enlight_Components_Cron_CronJob $job)
	{
		$this->_adapter->deactivateJob($job);
		return $this;
	}

	public static function getCronAction($name)
	{
		return $name = 'Enlight_Test_CronJob'.str_replace(' ','',ucwords(str_replace('_',' ',$name)));
	}


}
