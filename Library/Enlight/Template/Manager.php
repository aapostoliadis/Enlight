<?php
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
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

require_once('Smarty/Smarty.class.php');

/**
 * @category   Enlight
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Template_Manager extends Smarty
{
    /**
     * The name of class used for templates
     *
     * @var string
     */
    public $template_class = 'Enlight_Template_Handler';

	//public $allow_phptemplates = true;
	//public $allow_php_tag = true;

    /**
     * Class constructor, initializes basic smarty properties
     */
    public function __construct()
    {
    	// self pointer needed by some other class methods
        $this->smarty = $this;
        $this->start_time = microtime(true);
        
        // set default dirs
        $this->setTemplateDir('.' . DS . 'templates' . DS)
            ->setCompileDir('.' . DS . 'templates_c' . DS)
            ->setPluginsDir(array(dirname(__FILE__) . '/Plugins/', SMARTY_PLUGINS_DIR))
            ->setCacheDir('.' . DS . 'cache' . DS)
            ->setConfigDir('.' . DS . 'configs' . DS);

        $this->debug_tpl = 'file:' . dirname(__FILE__) . '/debug.tpl';
    }
}