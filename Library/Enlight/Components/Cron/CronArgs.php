<?php

class Enlight_Components_Cron_CronArgs extends Enlight_Event_EventArgs
{
	protected  $job;
	public function __construct($job)
	{
		parent::__construct($job->getAction(), $job->getData());
	}

	public function Job()
	{
		return $this->job;
	}
}
