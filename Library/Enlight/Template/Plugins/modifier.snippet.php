<?php
function smarty_modifier_snippet($content, $name=null, $namespace=null, $force=false)
{	
	if(!Enlight_Application::Instance()->Bootstrap()->hasResource('Snippets')) {
		return $content;
	}
			
	$snippet = Enlight_Application::Instance()->Snippets()->getSnippet($namespace);
    
    $content = html_entity_decode($content, ENT_QUOTES, mb_internal_encoding());
    $name = $name!==null ? $name : $content;
    
    $result = $snippet->get($name);
	if($result===null || $force) {
		$snippet->insert($name, $content); 
	} else {
		$content = $result;
	}
	
	return $content;
}