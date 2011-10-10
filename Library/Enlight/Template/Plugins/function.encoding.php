<?php
function smarty_function_encoding($params, $smarty, $template)
{
	return mb_internal_encoding();
}