<?php
function smarty_modifier_url($params=array())
{
	$front = Enlight::Instance()->Bootstrap()->getResource('Front');
	
	return $front->Router()->assemble($params);
}