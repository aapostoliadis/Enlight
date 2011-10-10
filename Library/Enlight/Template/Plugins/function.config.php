<?php
function smarty_function_config($params, $smarty, $template)
{
	if(empty($params['name'])
	  || !Enlight()->Bootstrap()->hasResource('Config')) {
		return null;
	}
	return Enlight()->Config()->get(
		$params['name'],
		isset($params['default']) ? $params['default'] : null
	);
}