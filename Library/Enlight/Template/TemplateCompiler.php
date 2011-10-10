<?php
require_once('Smarty/Smarty.class.php');

class Enlight_Template_TemplateCompiler extends Smarty_Internal_SmartyTemplateCompiler
{
	public $compile_tags = array('block'=>'Enlight_Template_BlockCompiler', 'blockclose'=>'Enlight_Template_BlockCloseCompiler');
	public function callTagCompiler($tag, $args, $param1 = null, $param2 = null, $param3 = null)
    {
    	if(isset($this->compile_tags[$tag]) && !isset(self::$_tag_objects[$tag])) {
    		self::$_tag_objects[$tag] =  new $this->compile_tags[$tag]; 
    	}
    	return parent::callTagCompiler($tag, $args, $param1, $param2, $param3);
    }
}