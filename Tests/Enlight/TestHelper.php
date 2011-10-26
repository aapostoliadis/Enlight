<?php
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(dirname(__FILE__) . '/../../Library/'),
    realpath(dirname(__FILE__) . '/../')
)));

include_once 'Enlight/Application.php';

/**
 * Shopware Test Helper
 *
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @subpackage Enlight_Tests
 */
class Enlight_TestHelper extends Enlight_Application
{
	/**
	 * The test path
	 *
	 * @var string
	 */
	protected $testPath;

	/**
	 * The Test Helper Instance
	 *
	 * @var string
	 */
	protected static $_instance;

	/**
	 * Constructor method
	 *
	 * Loads all needed resources for the test.
	 */
	public function __construct()
	{
		$this->testPath = dirname(__FILE__) . $this->DS();

		parent::__construct('testing', array(
			'app' => 'Default',
			'app_path' => realpath($this->testPath. '../../Apps/Default/')
		));

		$this->Bootstrap()->loadResource('Zend');
		//$this->Bootstrap()->loadResource('Cache');
		//$this->Bootstrap()->loadResource('Db');
		//$this->Bootstrap()->loadResource('Plugins');
		//$this->Bootstrap()->loadResource('Session');

		//$this->Loader()->loadClass('Shopware_Components_Test_TicketListener');
		//$this->Loader()->loadClass('Shopware_Components_Test_MailListener');

		//$this->Config()->hostOriginal = $this->Config()->host;
		//$this->Bootstrap()->loadResource('License');
	}

	/**
	 * Returns the path to test directory.
	 *
	 * @param string $path
	 * @return string
	 */
	public function TestPath($path=null)
	{
		if($path!==null) {
			$path = str_replace('_', $this->DS(), $path);
			return $this->testPath . $path . $this->DS();
		}
		return $this->testPath;
	}

	/**
	 * Returns the singleton instance of the tests helper.
	 *
	 * @return Enlight_TestHelper
	 */
	public static function Instance()
	{
		if(self::$_instance===null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
}

/**
 * Start test application
 */
Enlight_TestHelper::Instance();