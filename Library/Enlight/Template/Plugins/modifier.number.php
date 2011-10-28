<?php
function smarty_modifier_number($value, $format=array())
{
	if(empty($format['locale'])) {
		$format['locale'] = Enlight_Application::Instance()->Locale();
	}
	return Zend_Locale_Format::toNumber($value, $format);
}