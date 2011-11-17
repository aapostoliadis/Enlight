<?php
class Enlight_Extensions_JsonRenderer_Bootstrap extends Enlight_Plugin_Bootstrap_Config
{
	/**
	 * Should this plugin be active? Default is false.
	 *
	 * @var bool
	 */
	private $enabled = false;

	/**
	 * Install Method for any plugin
	 *
	 * @return void
	 */
//	public function install()
//	{
//		$foo = $this->subscribeEvent(
//            'Enlight_Controller_Front_PostDispatch',
//            null,
//            'onPostDispatch'
//        );
//	}

	/**
	 * Default Plugin Init Method.
	 * This Method is mandatory!
	 *
	 * @return void
	 */
//	public function init()
//	{
//		// Create a new Event listener. Callback method called 'onSendResponse'
//	 	$event = new Enlight_Event_Handler_Default(
//	 		'Enlight_Controller_Front_PostDispatch',
//             null,
//	 		array($this, 'onPostDispatch')
//	 	);
//
//		/** @var $namespace Enlight_Plugin_Namespace_Config */
//		$namespace = $this->Collection();
//		$namespace->Subscriber()->registerListener($event);
//	}

	/**
	 * Event listener - as defined at the init method.
	 *
	 * @param Enlight_Event_EventArgs $args
	 * @return bool
	 */
	public function onPostDispatch(Enlight_Event_EventArgs $args)
	{
		if($this->isEnabled()){
			/** @var $subject Enlight_Controller_Front */
			$subject = $args->get('subject');

			/** @var Enlight_Controller_Response_Response $response	 */
			$response = $subject->Response();

			/** @var $viewRenderer  Enlight_Controller_Plugins_ViewRenderer_Bootstrap*/
			$viewRenderer = $subject->getParam('controllerPlugins')->ViewRenderer();
			$assignedData = (array)$viewRenderer->View()->getAssign();
			$utf8data = $this->convertToUtf8($assignedData);

			$response->setHeader('Content-type', 'application/json', true);
			$response->sendHeaders();
			$response->setBody(Zend_Json_Encoder::encode($utf8data, true));
		}
	}

	private function convertToUtf8(array $data)
	{
		foreach($data as $key=>$value)
		{
			if(is_array($value)) {
				$data[$key] = $this->convertToUtf8($value);
			} else {
				$encoding = mb_detect_encoding($value);
				if($encoding != 'UTF-8') {
					$data[$key] =  iconv($encoding, 'UTF-8', $value);
				}
			}
		}
		return $data;
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
	 * Define if this plugin is active. Default: true
	 * To disable this plugin use false as parameter.
	 *
	 * @param bool $enabled
	 * @return Enlight_Extensions_JsonRenderer_Bootstrap
	 */
	public function setEnabled($enabled = true)
	{
		$this->enabled = (bool)$enabled;
		return $this;
	}

	/**
	 * Default Method - Convert this Plugin in an array. But this is currently not implemented.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array();
	}
}