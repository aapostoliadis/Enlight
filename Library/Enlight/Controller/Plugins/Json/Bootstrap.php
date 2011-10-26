<?php
class Enlight_Controller_Plugins_Json_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
	public function init()
	{		
	 	$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Front_SendResponse',
             null,
	 		array($this, 'onSendResponse')
	 	);
		Enlight()->Events()->registerListener($event);
	}

	protected $padding;
	
	public function onSendResponse(Enlight_Event_EventArgs $args)
	{
		if(empty($this->padding)) {
			return;
		}
		
		if($this->padding===true) {
			$callback = $args->getRequest()->getParam('callback');
			$callback = preg_replace('#[^0-9a-z]+#i', '', (string) $callback);
		} else {
			$callback = $this->padding;
		}		

		if(empty($callback)) {
			return;
		}
		
		$response = $args->getResponse();
		
		$response->sendHeaders();
		echo $callback.'("';
		echo strtr($response->getBody(), array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));
        echo '");';
		
		return true;
	}
	
	public function setPadding($padding = true)
	{
		$this->padding = $padding;
	}
}