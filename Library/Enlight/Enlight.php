<?php
require_once(dirname(__FILE__).'/Application.php');

/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

/**
 * @category   Enlight
 * @package    Enlight
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
if(!class_exists('Enlight'))
{
	class Enlight extends Enlight_Application
	{
        /**
         * Constructor
         * @param $environment
         * @param null $options
         */
		public function __construct($environment, $options = null)
		{
			Enlight($this);
			parent::__construct($environment, $options);
		}
	}
}
/**
 * Enlight
 *
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 * @package Enlight
 * @param null $newInstance
 * @return Enlight
 */
function Enlight($newInstance=null)
{
	static $instance;
	if(isset($newInstance)) {
		$oldInstance = $instance;
		$instance = $newInstance;
		return $oldInstance;
	}
	elseif(!isset($instance)) {
		$instance = Enlight::Instance();
	}
	return $instance;
}