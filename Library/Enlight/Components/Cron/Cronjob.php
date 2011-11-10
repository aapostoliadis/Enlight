<?php
class Enlight_Components_Cron_CronJob extends Enlight_Event_EventArgs
{
	//public $data = null;

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}