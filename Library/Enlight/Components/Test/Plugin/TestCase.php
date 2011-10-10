<?php
/**
 * Plugin test case
 *
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Tests
 */
abstract class Enlight_Components_Test_Plugin_TestCase extends Enlight_Components_Test_Controller_TestCase
{
	/**
	 * Create event args method
	 *
	 * @param string|array $name|$args
	 * @param array $args
	 * @return Enlight_Event_EventArgs
	 */
	public function createEventArgs($name=null, $args=array())
	{
		if($name===null) {
			$name = get_class($this);
		} elseif (is_array($name)) {
			$args = $name;
			$name = get_class($this);
		}
		return new Enlight_Event_EventArgs($name, $args);
	}
}