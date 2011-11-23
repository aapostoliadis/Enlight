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

class Enlight_Components_Cron_Adapter_DbTable extends Zend_Db_Table_Abstract implements Enlight_Components_Cron_Adapter
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_name = 's_crontab';

    /**
	 * @var string
	 */
	protected $_primary = 'id';

	protected $_columns = array(
		'id' => 'id',
		'name' => 'name',
		'action' => 'action',
		'elementID' => 'elementID',
		'data' => 'data',
		'next' => 'next',
		'start' => 'start',
		'interval' => 'interval',
		'active' => 'active',
		'end' => 'end'
	);

	public function __construct(Array $options = null)
	{
		if (null !== $options) {
			$this->setOptions($options);
		}
	}

	public function setOptions(array $options)
    {
        foreach ($options as $key => $option) {
        	if(substr($key, -6) == 'Column') {
        		$this->_columns[substr($key, 0, -6)] = (string) $option;
        	}
        }
        return parent::setOptions($options);
    }


	/**
	 * Deactivate a given Cron Job in the crontab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function disableJob(Enlight_Components_Cron_Job $job)
	{
		$job->setActive(false);
		
		return $this->updateJob($job);
	}

	/**
	 * Updates a cron job in the cron tab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function updateJob(Enlight_Components_Cron_Job $job)
	{
		$db = $this->_db;

		$sql = 'UPDATE '.$this->_crontab.'
				SET
					`'.$this->_name.'` = ?,
					`'.$this->_action.'` = ?,
					`'.$this->_data.'` = ?,
					`'.$this->_next.'` = ?,
					`'.$this->_start.'` = ?,
					`'.$this->_interval.'` = ?,
					`'.$this->_active.'` = ?,
					`'.$this->_end.'` = ?
				WHERE
					`'.$this->_id.'` = '.$job->getId().'
		';

		$db->query($sql, array(
			$job->getName(),
			$job->getAction(),
			serialize($job->getData()),
			$job->getNext()->get(),
			$job->getStart()->get(),
			$job->getInterval(),
			$job->isActive(),
			$job->getEnd()->get()
		));

		return $this;
	}

	/**
	 * Returns an array of Enlight_Components_Cron_Job from the crontab
	 * If no cron jobs found the method will return an empty array
	 *
	 * @param bool $ignoreActive if set true the active flag will be ignored
	 * @return array
	 */


	public function getAllJobs($ignoreActive = false)
	{
		$db = $this->_db;
		if($ignoreActive){
			$where = '`'.$this->_active.'` = 1';
		}
		else
		{
			$where = ' 1 ';
		}
		$sql = '
			SELECT
				`'.$this->_id.'`,
				`'.$this->_name.'`,
				`'.$this->_action.'`,
				`'.$this->_data.'`,
				`'.$this->_next.'`,
				`'.$this->_start.'`,
				`'.$this->_interval.'`,
				`'.$this->_active.'`,
				`'.$this->_end.'`
			FROM
				'.$this->_crontab.'
			WHERE '.$where.'
		';
		$jobs =  $db->fetchAll($sql);
		$retVal = array();
		foreach ($jobs as $jobData) {
			$jobData['next'] = new Zend_Date($jobData['next']);
			$jobData['start'] = new Zend_Date($jobData['start']);
			$jobData['end'] = new Zend_Date($jobData['end']);
			$job['data']  = unserialize($jobData['data']);
		    $retVal[$jobData['id']] = new Enlight_Components_Cron_Job($jobData);
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
	 * @return null|Enlight_Components_Cron_Job
	 */
	public function getNextJob()
	{
		$db = $this->_db;
		$sql = '
			SELECT `'.$this->_id.'`,
				`'.$this->_name.'`,
				`'.$this->_action.'`,
				`'.$this->_data.'`,
				`'.$this->_next.'`,
				`'.$this->_start.'`,
				`'.$this->_interval.'`,
				`'.$this->_active.'`,
				`'.$this->_end.'`
			FROM
				'.$this->_crontab.'
			WHERE
				`'.$this->_active.'`= 1
				AND `'.$this->_next.'` < ?
				AND `'.$this->_end.'` IS NOT NULL
			ORDER BY
				`'.$this->_next.'`
		';
		
		// collect cron jobs from the database
		$job = $db->fetchRow($sql, array(date('Y-m-d H:i:s', time())));
		// if we did not found any, return null
		if(empty($job)){
			return null;
		}
		$job['next']  = new Zend_Date($job['next']);
		$job['start'] = new Zend_Date($job['start']);
		$job['end']   = new Zend_Date($job['end']);
		$job['data']  = unserialize($job['data']);
		$job = new Enlight_Components_Cron_Job($job);

		return $job;
	}

	/**
	 * Receives a single Cron job defined by its id from the crontab
	 *
	 * @param Int $id
	 * @return Enlight_Components_Cron_Job|null
	 */
	public function getJobById($id)
	{
		$db = $this->_db;
		$sql = '
			SELECT
				`'.$this->_id.'`,
				`'.$this->_name.'`,
				`'.$this->_action.'`,
				`'.$this->_data.'`,
				`'.$this->_next.'`,
				`'.$this->_start.'`,
				`'.$this->_interval.'`,
				`'.$this->_active.'`,
				`'.$this->_end.'`
			FROM
				'.$this->_crontab.'
			WHERE `'.$this->_id.'` = ?
		';
		// collect cron jobs from the database
		$job = $db->fetchRow($sql, $id);
		// if we did not found any, return null
		if(empty($job)){
			return null;
		}
		$job['next']  = new Zend_Date($job['next']);
		$job['start'] = new Zend_Date($job['start']);
		$job['end']   = new Zend_Date($job['end']);
		$job['data']  = unserialize($job['data']);
		$job = new Enlight_Components_Cron_Job($job);
		return $job;
	}

	/**
	 * Receives a single cron job by its name
	 *
	 * @param String $name
	 * @return Enlight_Components_Cron_Job
	 */
	public function getJobByName($name)
	{
		$db = $this->_db;
		$sql = '
			SELECT
				`'.$this->_id.'`,
				`'.$this->_name.'`,
				`'.$this->_action.'`,
				`'.$this->_data.'`,
				`'.$this->_next.'`,
				`'.$this->_start.'`,
				`'.$this->_interval.'`,
				`'.$this->_active.'`,
				`'.$this->_end.'`
			FROM
				'.$this->_crontab.'
			WHERE
				`'.$this->_name.'` = ?
		';
		// collect cron jobs from the database
		$job = $db->fetchRow($sql, $name);

		// if we did not found any, return null
		if(empty($job)){
			return null;
		}
		$job['next']  = new Zend_Date($job['next']);
		$job['start'] = new Zend_Date($job['start']);
		$job['end']   = new Zend_Date($job['end']);
		$job['data']  = unserialize($job['data']);
		$job = new Enlight_Components_Cron_Job($job);

		return $job;
	}

	/**
	 * Adds a job to the crontab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return Enlight_Components_Cron_Adapter_Adapter
	 */
	public function addJob(Enlight_Components_Cron_Job $job)
	{
		$db = $this->_db;

		$next = $job->getNext();
		if ( empty($next) ){
			$job->setNext(new Zend_Date());
		}
		$start = $job->getStart();
		if (empty($start)) {
			$zd = new Zend_Date();
			$job->setStart($zd->subDay(1));
		}
		$end = $job->getEnd();
		if (empty($end)) {
			$zd = new Zend_Date();
			$job->setEnd($zd->subDay(1));
		}

		$sql = '
				INSERT INTO '.$this->_crontab.'
					(`'.$this->_id.'`,
					`'.$this->_name.'`,
					`'.$this->_action.'`,
					`'.$this->_data.'`,
					`'.$this->_next.'`,
					`'.$this->_start.'`,
					`'.$this->_interval.'`,
					`'.$this->_active.'`,
					`'.$this->_end.'`)
				VALUES (?,?,?,?,?,?,?,?,?)
		';
		$db->query($sql, array(
			$job->getId(),
			$job->getName(),
			$job->getAction(),
			serialize($job->getData()),
			$job->getNext()->get(),
			$job->getStart()->get(), 
			$job->getInterval(),
			$job->isActive(),
			$job->getEnd()->get()
		));
		return $this;
	}

	/**
	 * Removes an job from the crontab
	 *
	 * @param Enlight_Components_Cron_Job $job
	 * @return \Enlight_Components_Cron_Adapter_Adapter
	 */
	public function removeJob(Enlight_Components_Cron_Job $job)
	{
		$db = $this->_db;
		$sql = '
			DELETE FROM '.$this->_crontab.'
			WHERE `'.$this->_id.'` = ?
			AND `'.$this->_action.'` = ?
		';
		
		$db->query($sql, array($job->getId(), $job->getAction()));
		return $this;
	}

	/**
	 * Receives a single job by its defined action
	 *
	 * @param $actionName
	 * @return Enlight_Components_Cron_Adapter_Adapter|null
	 */
	public function getJobByAction($actionName)
	{
		$db = $this->_db;
		$sql = '
			SELECT
				`'.$this->_id.'`,
				`'.$this->_name.'`,
				`'.$this->_action.'`,
				`'.$this->_data.'`,
				`'.$this->_next.'`,
				`'.$this->_start.'`,
				`'.$this->_interval.'`,
				`'.$this->_active.'`,
				`'.$this->_end.'`
			FROM '.$this->_crontab.' WHERE `'.$this->_action.'` = ?
		';
		// collect cron jobs from the database
		$job = $db->fetchRow($sql, $actionName);
		// if we did not found any, return null
		if(empty($job)){
			return null;
		}
		$job['next']  = new Zend_Date($job['next']);
		$job['start'] = new Zend_Date($job['start']);
		$job['end']   = new Zend_Date($job['end']);
		$job['data']  = unserialize($job['data']);
		$job = new Enlight_Components_Cron_Job($job);

		return $job;
	}
}