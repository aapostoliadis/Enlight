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
interface Enlight_Components_Cron_Adapter_Adapter
{

	/**
	 * Used to set adapter specific options
	 *
	 * @abstract
	 * @param array $options
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function setOptions(array $options);

	/**
	 * Deactivate a given Cron Job in the crontab
	 *
	 * @abstract
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function deactivateJob(Enlight_Components_Cron_CronJob $job);

	/**
	 * Updates a cron job in the cron tab
	 *
	 * @abstract
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_CronManager
	 */
	public function updateJob(Enlight_Components_Cron_CronJob $job);

	/**
	 * Returns an array of Enlight_Components_Cron_CronJob from the crontab
	 *
	 * @abstract
	 * @return array
	 */
	public function getAllCronJobs();

	/**
	 * Returns the next cron job
	 *
	 * @return null|Enlight_Components_Cron_CronJob
	 */
	public function getNextCronJob();

	/**
	 * Receives a single Cron job defined by its id from the crontab
	 *
	 * @abstract
	 * @param Int $id
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function getCronJobById($id);

	/**
	 * Receives a single cron job by its name
	 *
	 * @abstract
	 * @param String $name
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function getCronJobByName($name);

	/**
	 * Adds a job to the crontab
	 *
	 * @abstract
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	public function addCronJob(Enlight_Components_Cron_CronJob $job);

	/**
	 * Removes an job from the crontab
	 *
	 * @abstract
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return void
	 */
	public function deleteCronJob(Enlight_Components_Cron_CronJob $job);

	/**
	 * Receives a single job by its defined action
	 *
	 * @abstract
	 * @param $actionName
	 * @return void
	 */
	public function getCronJobByAction($actionName);

}