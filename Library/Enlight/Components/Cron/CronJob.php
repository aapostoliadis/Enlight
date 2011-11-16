<?php
class Enlight_Components_Cron_CronJob
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
	/**
	 * @var String
	 */
	protected $crontab;


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
				case 'crontab':
					$this->setCrontab($value);
					break;
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
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setData($data)
	{
		if(is_array($data) || is_object($data)){
			$this->data = serialize($data);
		}
		else{
			$this->data = $data;
		}

		return $this;
	}

	/**
	 * Reads the data field
	 *
	 * @return String
	 */
	public function getData()
	{
		#if(empty($this->data)) return array();
		#return unserialize($this->data);
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
	 * @return Enlight_Components_Cron_CronJob
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
	 * @param \String $name
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setName($name)
	{
		$this->name = (string)$name;
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param \String $action
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setAction($action)
	{
		$this->action = (string)$action;
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getNext()
	{
		return $this->next;
	}

	/**
	 * @param \String $next
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setNext($next)
	{
		if( !preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $next) ) {
			throw new Enlight_Exception('Wrong Data type given. Expected Data type: Datetime (0000-00-00 00:00:00)');
		}
		$this->next =(string)$next;
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * @param \String $start
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setStart($start)
	{
		if( !preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $start) ) {
			throw new Enlight_Exception('Wrong Data type given. Expected Data type: Datetime (0000-00-00 00:00:00)');
		}
		$this->start = (string)$start;
		return $this;
	}

	/**
	 * @return \String
	 */
	public function getEnd()
	{
		return $this->end;
	}

	/**
	 * @param \String $end
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setEnd($end)
	{
		if((!is_null($end) || !empty($end))&& !preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $end) ) {
			throw new Enlight_Exception('Wrong Data type given. Expected Data type: Datetime (0000-00-00 00:00:00)');
		}
		if(empty($end)) $end = null;
		$this->end = $end;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getInterval()
	{
		return $this->interval;
	}

	/**
	 * @param int $interval
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setInterval($interval)
	{
		$this->interval = (int)$interval;
		return $this;
	}

	/**
	 * @return int
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * @param int $active
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setActive($active)
	{
		$this->active = (boolean)$active;
		return $this;
	}

	/**
	 * @return String
	 */
	public function getCrontab()
	{
		return $this->crontab;
	}

	/**
	 * @param \String $crontab
	 * @return Enlight_Components_Cron_CronJob
	 */
	public function setCrontab($crontab)
	{
		$this->crontab = (string)$crontab;
		return $this;
	}
}