<?php
require_once('Smarty/Smarty.class.php');

/**
 * Enlight Template Manager
 * 
 * @link http://www.shopware.de
 * @copyright Copyright (c) 2011, shopware AG
 * @author Heiner Lohaus
 */
class Enlight_Template_TemplateManager extends Smarty
{
    public $ignore_namespace = false;
	//public $default_resource_type = 'extends';
	//public $template_class = 'Enlight_Template_TemplateDefault';

	public $allow_phptemplates = true;
	public $allow_php_tag = true;
    
    /**
     * Class constructor, initializes basic smarty properties
     */
    public function __construct()
    {
    	if (function_exists('mb_internal_encoding')) {
    		$encoding = mb_internal_encoding();
        }
		parent::__construct();
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding($encoding);
        }
        $this->plugins_dir = array(dirname(__FILE__).'/Plugins/', SMARTY_PLUGINS_DIR);
    }
}