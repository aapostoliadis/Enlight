<?php

class Enlight_Components_Cron_CronArgs extends Enlight_Event_EventArgs
{
	/** @var Enlight_Components_Cron_Job */
	protected  $job;
	public function __construct(Enlight_Components_Cron_Job $job)
	{
		$data = $job->getData();
		if(is_string($data)) {
			$data = unserialize($data);
		}
		$this->job = $job;
		parent::__construct($job->getAction(), $data);
	}

	public function Job()
	{
		return $this->job;
	}
}
