<?php
interface Enlight_View_View
{	
	public function setTemplateDir($path);
	public function addTemplateDir($path);
	public function setTemplate($template=null);
	public function hasTemplate();
	
	public function assign($spec, $value = null, $nocache = false, $scope = null);
	
	public function clearAssign($spec = null);
	public function getAssign($spec = null);
	
	public function render();
}