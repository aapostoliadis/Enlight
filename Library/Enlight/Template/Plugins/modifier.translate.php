<?php
function smarty_modifier_translate ($value = null, $path = null, $locale = null)
{
	if(!Enlight::Instance()->Bootstrap()->hasResource('Locale')) {
		return $value;
	}
	if($locale === null) {
		$locale = Enlight::Instance()->Locale();
	}
	if($path=='currency') {
		$path = 'nametocurrency';
	}
    return $locale->getTranslation($value, $path, $locale);
}