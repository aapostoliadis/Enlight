<?php
function smarty_modifier_padding ($str, $width=10, $break='...', $fill=' ')
{
	if(!is_scalar($break)) {
		$break = '...';
	}
	if(empty($fill) || !is_scalar($fill)) {
		$fill = ' ';
	}
	if(empty($width) || !is_numeric($width)) {
		$width = 10;
	} else { 
		$width = (int) $width;
	}
    
    //printf('% ' . $width . ' s' . $str);

	if(!is_scalar($str)) {
		return str_repeat($fill, $width);
	}
	if(strlen($str) > $width) {
		$str = substr($str, 0, $width - strlen($break)) . $break;
	}
	if($width > strlen($str)) {
		return str_repeat($fill,$width-strlen($str)) . $str;
	} else { 
		return $str;
	}
}