<?php
class Enlight_Controller_Plugins_Json_Bootstrap extends Enlight_Plugin_Bootstrap_Default
{
	/**
	 * Init this plugin. This Plugin should run after the ViewRenderer Plugin
	 *
	 * @return void
	 */

	public function init()
	{		
	 	$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Action_PostDispatch',
             500,
	 		array($this, 'Enlight_Controller_Action_PostDispatch')
	 	);
		$this->Application()->Events()->registerListener($event);
	}


	protected $renderDataOnly = true;

	/**
	 * Should the JSON object be encapsulated into a javascript function
	 *
	 * @var String
	 */
	protected $padding;
	
	/**
	 * Called from the Event Manager after the dispatch process
	 *
	 * @param Enlight_Event_EventArgs $args
	 * @return bool
	 */
	public function Enlight_Controller_Action_PostDispatch(Enlight_Event_EventArgs $args)
	{
		// We do not need to render the whole page
		$this->Collection()->ViewRenderer()->setNoRender(true);

		// If the attribute padding is a boolean true
		if($this->padding===true) {
			$callback = $args->getRequest()->getParam('callback');
			$callback = preg_replace('#[^0-9a-z]+#i', '', (string) $callback);
		} else{
			$callback = $this->padding;
		}

		/** @var $subject Enlight_Controller_Front */
		$subject = $args->get('subject');
		/** @var Enlight_Controller_Response_Response $response	 */
		$response = $subject->Response();
		// decide if we should render the data or the whole page
		if($this->renderDataOnly){
			$content = $subject->View()->getAssign();
			$content = $this->convertToUtf8($content);
		} else {
			$content =  array('data'=>$response->getBody());
			$content = $this->convertToUtf8($content);
			$content = $content['data'];
		}

		if(!$this->renderDataOnly) {
			$jsonData = $content;
		} else {
			$jsonData = Zend_Json::encode($content);
		}
		
		if($this->padding){
			$jsonData = $this->addPadding($jsonData, $callback);
		}

		if($this->padding && !empty($callback)){
				$response->setHeader('Content-type', 'text/javascript', true);
		} else {
			$response->setHeader('Content-type', 'application/json', true);
		}
		$response->sendHeaders();
		$response->setBody($jsonData);

		return true;
	}
	
	/**
	 * Sometimes it is necessary to pad an JSON object into a javascript function. If this behaviour need
	 * this method can be called with a true value as parameter to enable the padding mode.
	 * If this mode is active the system takes the name found in the GET parameter 'callback' as the javascript function
	 * name.
	 * Eg.: A call to /jsonTest/?callback=foo result in
	 * 		foo({"anArray":[["element1","element2"],"element3","element"],"controllerGreetings":"Hello Enlight","SCRIPT_NAME":"\/index.php"});
	 *
	 * @param bool $padding
	 * @return void
	 */
	public function setPadding($padding = true)
	{
		$this->padding = $padding;
	}
	/**
	 * Check if this plugin is enabled
	 *	 *
	 * @return boolean
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * The method can be used to determine if the raw output will be transformed to JSON
	 * or just the data assigned to the current view.
	 *
	 * @param bool $renderData
	 * @return void
	 */
	public function setRenderer($renderData = true)
	{
		$this->renderDataOnly = $renderData;
	}

	/**
	 * Converts an non UTF-8 string in to an UTF-8 string
	 *
	 * @param array $data
	 * @param string $encoding
	 * @return array
	 */
	private function convertToUtf8($data, $encoding='')
	{
		if(is_string($data))		{
			return mb_convert_encoding($data, 'UTF-8', $encoding);
		}
		foreach($data as $key => $value)
		{
			if(is_array($value)) {
				$data[$key] = $this->convertToUtf8($value);
			} else {
				if(empty($encoding)) {
					$encoding = mb_detect_encoding($value);
				}				
				if($encoding != 'UTF-8') {
					$data[mb_convert_encoding($key, 'UTF-8', $encoding)] =  mb_convert_encoding($value, 'UTF-8', $encoding);
				}
			}
		}
		return $data;
	}

	/**
	 * Embedded a JSON object into an callback function
	 *
	 * @param $data
	 * @param $callback
	 * @return String
	 */
	private function addPadding($data, $callback)
	{
		if(empty($callback)){
			return $data;
		}
		$retVal = $callback."(".Zend_Json::encode($data).");";
		return $retVal;
	}
}