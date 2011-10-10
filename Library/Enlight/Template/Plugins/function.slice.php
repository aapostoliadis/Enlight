<?php
function smarty_function_slice($params, $smarty)
{
	$array = array_slice($params['array'],$params['offset'],$params['lenght']);
	$smarty->assign($params['assign'],$array);
}