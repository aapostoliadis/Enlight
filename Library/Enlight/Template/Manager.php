<?php
require_once('Smarty/Smarty.class.php');

/**
 * Enlight Template Manager
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
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
    	if (function_exists('mb_internal_encoding')) {
    		$encoding = mb_internal_encoding();
            parent::__construct();
            mb_internal_encoding($encoding);
        } else {
            parent::__construct();
        }
        $this->plugins_dir = array(dirname(__FILE__) . '/Plugins/', SMARTY_PLUGINS_DIR);
    }
}