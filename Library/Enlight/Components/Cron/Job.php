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
class Enlight_Components_Cron_Job
{
	/**
	 * @var Integer ID of the cronjob
	 */
	protected $id;
	/**
	 * @var String
	 */
	protected $name;
	/**
	 * @var String
	 */
	protected $action;
	/**
	 * @var String
	 */
	protected $data;
	/**
	 * @var String MySQL datetime
	 */
	protected $next;
	/**
	 * @var String MySQL datetime
	 */
	protected $start;
	/**
	 * @var String MySQL datetime
	 */
	protected $end;
	/**
	 * @var Integer
	 */
	protected $interval;
	/**
	 * @var Integer
	 */
	protected $active;
//	/**
//	 * @var String
//	 */
//	protected $crontab;

	/**
	 * This is a Cronjob Model. Following option must be provided in the options array
	 *
	 * id - unique identifier for a specific cronjob eg. autoincrement database field. Expected data type: Integer
	 * interval - Time interval in minutes the cronjob is scheduled to run
	 * next - The next time the cronjob is due. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * start - The start time of the cronjob. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * end - The time the last scheduled run ended. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * active - Boolean value which indicates if a cronjob is enabled or not. If the cronjob is disabled the cronjob
	 *          will not be executed. Expected data type: boolean
	 * crontab - Name of the storage where all the cron jobs are stored: Expected data type: String
	 * name - Name or the description of the cron job: Expected data type: String
	 * action - Name of the action which is called during the execution phase. Expected data type: String
	 * data - Data storage. Can be used to store answers from cron job call. Expected data type: String
	 * 
	 *
	 * @param array $options
	 */
	public function __construct(array $options )
	{
		$this->setOptions($options);
		if(!isset($this->data) || empty($this->data)){
			$this->data = array();
		}
	}

	/**
	 * id - unique identifier for a specific cronjob eg. autoincrement database field. Expected data type: Integer
	 * interval - Time interval in minutes the cronjob is scheduled to run
	 * next - The next time the cronjob is due. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * start - The start time of the cronjob. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * end - The time the last scheduled run ended. Expected data type: String in form of yyyy-mm-dd hh:mm:ss
	 * active - Boolean value which indicates if a cronjob is enabled or not. If the cronjob is disabled the cronjob
	 *          will not be executed. Expected data type: boolean
	 * crontab - Name of the storage where all the cron jobs are stored: Expected data type: String
	 * name - Name or the description of the cron job: Expected data type: String
	 * action - Name of the action which is called during the execution phase. Expected data type: String
	 * data - Data storage. Can be used to store answers from cron job call. Expected data type: String
	 * 
	 * @throws Enlight_Exception|Exception
	 * @param array $options
	 * @return void
	 */
	public function setOptions(array $options)
	{
		foreach($options as $fieldName=>$value)
		{
			switch($fieldName){
				case 'id':
					$this->setId($value);
					break;
				case 'interval':
					$this->setInterval($value);
					break;
				case 'next':
					$this->setNext($value);
					break;
				case 'start':
					$this->setStart($value);
					break;
				case 'end':
					$this->setEnd($value);
					break;
				case 'active':
					$this->setActive($value);
					break;
//				case 'crontab':
//					$this->setCrontab($value);
//					break;
				case 'name':
					$this->setName($value);
					break;
				case 'action':
					$this->setAction($value);
					break;
				case 'data':
					$this->setData($value);
					break;

				default:
					//$this->$fieldName = (string) $value;
			}
		}
	}

	/**
	 * Sets the data field
	 *
	 * @param $data
	 * @return Enlight_Components_Cron_Job
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this; 
	}

	/**
	 * Reads the data field
	 *
	 * @return String
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Enlight_Components_Cron_Job
	 */
	public function setId($id)
	{
		$this->id = (int)$id;
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param String $name
	 * @return Enlight_Components_Cron_Job
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
		return $this;
	}

	/**
	 * @return String
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param String $action
	 * @return Enlight_Components_Cron_Job
	 */
	public function setAction($action)
	{
		$this->action = (string)$action;
		return $this;
	}

	/**
	 * @return Zend_Date
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * Sets the next date when the cronjob is due
	 *
	 * @param Zend_Date $next
	 * @return Enlight_Components_Cron_Job
	 */
	public function setNext(Zend_Date $next)
	{
		$this->next = $next;
		return $this;
	}

	/**
	 * @return Zend_Date
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * Sets the date and time when the cronjob has been started
	 *
	 * @param Zend_Date $start
	 * @return Enlight_Components_Cron_Job
	 */
	public function setStart(Zend_Date $start)
	{
		$this->start = $start;
		return $this;
	}

	/**
	 * Returns the date and time when the cronjob finished its last run.
	 *
	 * @return Zend_Date
	 */
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * Sets the date and time when the cronjob stopped its run.
	 *
	 * @param null|Zend_Date $end
	 * @return Enlight_Components_Cron_Job
	 */
	public function setEnd(Zend_Date $end)
	{
		if(empty($end)) $end = null;
		$this->end = $end;
		return $this;
	}

	/**
	 * Returns the interval in seconds a cron has to be scheduled
	 *
	 * @return int
	 */
	public function getInterval()
	{
		return $this->interval;
	}

	/**
	 * Sets the interval in seconds a cron has to be scheduled
	 *
	 * @param int $interval
	 * @return Enlight_Components_Cron_Job
	 */
	public function setInterval($interval)
	{
		$this->interval = (int)$interval;
		return $this;
	}

	/**
	 * Checks if the cronjob is active
	 *
	 * @return int
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * Alias for isActive
	 *
	 * @return int
	 */
	public function getActive()
	{
		return $this->isActive();
	}

	/**
	 * Sets the cronjob either active or inactive
	 *
	 * @param bool $active
	 * @return Enlight_Components_Cron_Job
	 */
	public function setActive($active)
	{
		$this->active = (boolean)$active;
		return $this;
	}

//	/**
//	 * Returns the name of the crontab
//	 *
//	 * @return String
//	 */
//	public function getCrontab()
//	{
//		return $this->crontab;
//	}
//
//	/**
//	 * Sets the name of the crontab
//	 *
//	 * @param String $crontab
//	 * @return Enlight_Components_Cron_Job
//	 */
//	public function setCrontab($crontab)
//	{
//		$this->crontab = (string)$crontab;
//		return $this;
//	}

	public function run()
	{
		
	}

	public function __get($name)
	{
		return $this->{$name};
	}

}
