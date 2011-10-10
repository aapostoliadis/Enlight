<?php
function smarty_modifier_number($value, $format=array())
{
	if(empty($format['locale']))
	{
		$format['locale'] = Enlight()->Locale();
	}
	return Zend_Locale_Format::toNumber($value, $format);
}