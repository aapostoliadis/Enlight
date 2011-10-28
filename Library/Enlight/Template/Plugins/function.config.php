<?php
function smarty_function_config($params, $smarty, $template)
{
	if(empty($params['name'])
	  || !Enlight_Application::Instance()->Bootstrap()->hasResource('Config')) {
		return null;
	}
	return Enlight_Application::Instance()->Config()->get(
		$params['name'],
		isset($params['default']) ? $params['default'] : null
	);
}