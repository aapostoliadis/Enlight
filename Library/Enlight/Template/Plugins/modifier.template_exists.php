<?php
function smarty_modifier_template_exists($template)
{
	$engine = Enlight::Instance()->Bootstrap()->getResource('Template');
	return $engine->templateExists($template);
}