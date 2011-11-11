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

class Enlight_Components_Cron_Adapter_DbAdapter implements Enlight_Components_Cron_Adapter_Adapter
{
	/**
	 * Name of the cronjob id field
	 *
	 * @var int
	 */
	protected $_id = 'id';
	/**
	 * Name of the cronjob name field
	 * @var string
	 */
	protected $_name = 'name';
	/**
	 * Name of the action method to call
	 * @var string
	 */
	protected $_action = 'action';
	/**
	 * Name of the element ID
	 * @var int
	 */
	protected $_elementID = 'elementID';
	/**
	 * Name of the data field which holds a serialized array
	 * @var string
	 */
	protected $_data = 'data';
	/**
	 * Name of the next date field
	 * @var string MySQL Datetime
	 */
	protected $_next = 'next';
	/**
	 * Name of the start field which hold the time the cron has been started
	 * @var string MySQL Datetime
	 */
	protected $_start = 'start';
	/**
	 * Name of the field which holds the interval in which the cronjob should be running in minutes
	 * @var int
	 */
	protected $_interval = 'interval';
	/**
	 * Name of the field which determines if a cronjob be run or not (1 yes 0 no)
	 *
	 * @var int
	 */
	protected $_active = 'active';
	/**
	 * Name of the field which where the timestamp is stored of the last cronjob run
	 * @var string MySQL Datetime
	 */
	protected $_end = 'end';
	/**
	 * Name of the field which holds the name of the template which has to be informed that this cronjob has been called
	 * @var string
	 */
	protected $_inform_template = 'inform_template';
	/**
	 * Name of the field which contained the email address where the status report should be mailed.
	 *
	 * @var string email address
	 */
	protected $_inform_mail = 'inform_mail';
	/**
	 * Name of the field which holds the plugin ID
	 *
	 * @var int
	 */
	protected $_pluginID = 'pluginID';

	/**
	 * Name of the crontab table
	 *
	 * @var string
	 */
	protected $_crontab = 's_crontab';

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db;

	public function __construct(Array $options = array())
	{
		if (!empty($options))
			$this->setOptions($options);
	}

	public function __get($name)
	{
		if(isset($this->{'_'.$name}))
		{
			return $this->{'_'.$name};
		}
		return null;
	}

	/**
	 * Used to set adapter specific options
	 *
	 * @param array $options
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	 public function setOptions(array $options)
    {
        foreach ($options as $key=>$option) {
            switch ($key) {
				case 'pluginID':
				case 'active':
				case 'elementID':
				case 'interval':
                case 'id':
					if(!empty($option))
						$this->{'_'.$key} = (int)$option;
					break;
				case 'name':
				case 'action':
				case 'data':
					if(empty($option)){
						$option = " ";
					}
				case 'next':
				case 'start':
				case 'end':
				case 'inform_template':
				case 'crontab':
				case 'inform_mail':
					if(!empty($option))
                    	$this->{'_'.$key} = (string)$option;
                    break;
                case 'db':
					if( !empty($option) && ($option instanceof Zend_Db_Adapter_Abstract) )
                    	$this->{'_'.$key} = $option;
					break;
                default:
                    break;
            }
        }
        return $this;
    }

	/**
	 * Deactivate a given Cron Job in the crontab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function deactivateJob(Enlight_Components_Cron_CronJob $job)
	{
		$job->active = 0;
		
		return $this->updateJob($job);
	}

	/**
	 * Updates a cron job in the cron tab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function updateJob(Enlight_Components_Cron_CronJob $job)
	{
		$db = $this->_db;

		$sql = 'UPDATE '.$this->_crontab.'
				SET
					`name` = ?,
					`action` = ?,
					`elementID` = ?,
					`data` = ?,
					`next` = ?,
					`start` = ?,
					`interval` = ?,
					`active` = ?,
					`end` = ?,
					`inform_template` = ?,
					`inform_mail` = ?,
					`pluginID` = ?
				WHERE id = '.$job->id.'
		';
		$db->query($sql, array(
			$job->name,
			$job->action,
			$job->elementID,
			$job->data,
			$job->next,
			$job->start,
			$job->interval,
			$job->active,
			$job->end,
			$job->inform_template,
			$job->inform_mail,
			$job->pluginID
		));
		
		return $this;
	}

	/**
	 * Returns an array of Enlight_Components_Cron_CronJob from the crontab
	 * If no cron jobs found the method will return an empty array
	 *
	 * @param bool $ignoreActive if set true the active flag will be ignored
	 * @return array
	 */


	public function getAllCronJobs($ignoreActive = false)
	{
		$db = $this->_db;
		if($ignoreActive){
			$where = 'active = 1';
		}
		else
		{
			$where = ' 1 ';
		}
		$sql = '
			SELECT `id`, `name`, `action`, `elementID`, `data`, `next`, `start`, `interval`, `active`, `end`, `inform_template`, `inform_mail`, `pluginID`
			FROM '.$this->_crontab.' WHERE '.$where.'
		';
		$jobs =  $db->fetchAll($sql);

		$retVal = array();
		foreach ($jobs as $jobData) {
			$name = Enlight_Components_Cron_CronManager::getCronAction($jobData['action']);
		    $retVal[$jobData['id']] = new Enlight_Components_Cron_CronJob($name, $jobData);
		}

		if(empty($retVal)){
			return array();
		}
		else
		{
			return $retVal;
		}
	}

	/**
	 * Returns the next cron job
	 *
	 * @return null|Enlight_Components_Cron_CronJob
	 */
	public function getNextCronJob()
	{
		$db = $this->_db;
		$sql = '
			SELECT `id`, `name`, `action`, `elementID`, `data`, `next`, `start`, `interval`, `active`, `end`, `inform_template`, `inform_mail`, `pluginID`
			FROM '.$this->_crontab.' WHERE active=1 AND next < ? AND end IS NOT NULL
			ORDER BY next
		';
		// collect cron jobs from the database
		$jobs = $db->fetchRow($sql, array(date('Y-m-d H:i:s', time())));
		// if we did not found any, return null
		if(empty($jobs)){
			return null;
		}

		$name = Enlight_Components_Cron_CronManager::getCronAction($jobs['action']);
		$job = new Enlight_Components_Cron_CronJob($name, $jobs);

		return $job;
	}

	/**
	 * Receives a single Cron job defined by its id from the crontab
	 *
	 * @param Int $id
	 * @return Enlight_Components_Cron_CronJob|null
	 */
	public function getCronJobById($id)
	{
		$db = $this->_db;
		$sql = '
			SELECT `id`, `name`, `action`, `elementID`, `data`, `next`, `start`, `interval`, `active`, `end`, `inform_template`, `inform_mail`, `pluginID`
			FROM '.$this->_crontab.' WHERE id=?
		';
		// collect cron jobs from the database
		$jobs = $db->fetchRow($sql, $id);
		// if we did not found any, return null
		if(empty($jobs)){
			return null;
		}
		
		$name = Enlight_Components_Cron_CronManager::getCronAction($jobs['action']);
		$job = new Enlight_Components_Cron_CronJob($name, $jobs);
		
		return $job;
	}

	/**
	 * Receives a single cron job by its name
	 *
	 * @param String $name
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function getCronJobByName($name)
	{
		$db = $this->_db;
		$sql = '
			SELECT `id`, `name`, `action`, `elementID`, `data`, `next`, `start`, `interval`, `active`, `end`, `inform_template`, `inform_mail`, `pluginID`
			FROM '.$this->_crontab.' WHERE `name`=?
		';
		// collect cron jobs from the database
		$jobs = $db->fetchRow($sql, $name);
		// if we did not found any, return null
		if(empty($jobs)){
			return null;
		}
		$name = Enlight_Components_Cron_CronManager::getCronAction($jobs['action']);
		$job = new Enlight_Components_Cron_CronJob($name, $jobs);

		return $job;
	}

	/**
	 * Adds a job to the crontab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function addCronJob(Enlight_Components_Cron_CronJob $job)
	{
		$db = $this->_db;
		
		if (empty($job->next)) {
			$job->next = date('Y-m-d H:i:s', time());
		}
		if (empty($job->start)) {
			$job->start = date('Y-m-d H:i:s', time()-86400);
		}
		if (empty($job->end)) {
			$job->end = date('Y-m-d H:i:s', time()-86400);
		}
		
		$sql = '
				INSERT INTO '.$this->_crontab.'
					(`id`,
					`name`,
					`action`,
					`elementID`,
					`data`,
					`next`,
					`start`,
					`interval`,
					`active`,
					`end`,
					`inform_template`,
					`inform_mail`,
					`pluginID`)
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
		';
		$db->query($sql, array(
			$job->id,
			$job->name,
			$job->action,
			$job->elementID,
			$job->data,
			$job->next,
			$job->start,
			$job->interval,
			$job->active,
			$job->end,
			$job->inform_template,
			$job->inform_mail,
			$job->pluginID
		));
		return $this;
	}

	/**
	 * Removes an job from the crontab
	 *
	 * @param Enlight_Components_Cron_CronJob $job
	 * @return \Enlight_Components_Cron_Adapter_Adapter
	 */
	public function deleteCronJob(Enlight_Components_Cron_CronJob $job)
	{
		$db = $this->_db;
		$id = $job->id;
		$action = $job->action;

		$sql = '
			DELETE FROM '.$this->_crontab.'
			WHERE `id` = ?
			AND `action` = ?
		';

		$db->query($sql, array($job->id, $job->action));
				
		return $this;
	}

	/**
	 * Receives a single job by its defined action
	 *
	 * @param $actionName
	 * @return Enlight_Components_Cron_Adapter_Adapter|null
	 */
	public function getCronJobByAction($actionName)
	{
		$db = $this->_db;
		$sql = '
			SELECT `id`, `name`, `action`, `elementID`, `data`, `next`, `start`, `interval`, `active`, `end`, `inform_template`, `inform_mail`, `pluginID`
			FROM '.$this->_crontab.' WHERE `action`=?
		';
		// collect cron jobs from the database
		$jobs = $db->fetchRow($sql, $actionName);
		// if we did not found any, return null
		if(empty($jobs)){
			return null;
		}
		$name = Enlight_Components_Cron_CronManager::getCronAction($jobs['action']);
		$job = new Enlight_Components_Cron_CronJob($name, $jobs);

		return $job;
	}
}