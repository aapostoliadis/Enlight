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
	public function install()
	{
		$foo = $this->subscribeEvent(
            'Enlight_Controller_Front_SendResponse',
            null,
            'onSendResponse'
        );
	}

	/**
	 * Default Plugin Init Method.
	 * This Method is mandatory!
	 *
	 * @return void
	 */
	public function init()
	{
		// Create a new Event listener. Callback method called 'onSendResponse'
	 	$event = new Enlight_Event_Handler_Default(
	 		'Enlight_Controller_Front_SendResponse',
             null,
	 		array($this, 'onSendResponse')
	 	);

		/** @var $namespace Enlight_Plugin_Namespace_Config */
		$namespace = $this->Collection();
		$namespace->Subscriber()->registerListener($event);
	}

	/**
	 * Event listener - as defined at the init method.
	 *
	 * @param Enlight_Event_EventArgs $args
	 * @return bool
	 */
	public function onSendResponse(Enlight_Event_EventArgs $args)
	{
		if($this->isEnabled())
		{
			/** @var Enlight_Controller_Response_Response $response	 */
			$response = $args->getResponse();
			/** @var $subject Enlight_Controller_Front */
			$subject = $args->get('subject');

			/** @var $foo  Enlight_Controller_Plugins_ViewRenderer_Bootstrap*/
			$foo = $subject->getParam('controllerPlugins')->ViewRenderer();
			//$foo->setNoRender();
			$bar = $foo->View()->getAssign();
			/*
			die('Die in ' . basename(__FILE__) . '@' . __LINE__ . ' ' . print_r($bar, true));
			$response->setHeader('Content-type', 'application/json', true);
			$response->sendHeaders();
			echo json_encode('test');
			*/
			//exit;
		}

		return true;
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