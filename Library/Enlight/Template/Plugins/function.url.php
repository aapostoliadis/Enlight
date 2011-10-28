<?php
function smarty_function_url($params, $smarty, $template)
{
	$front = Enlight_Application::Instance()->Bootstrap()->getResource('Front');
	
	return $front->Router()->assemble($params);
}