<?php
function smarty_block_replace($params, $content, $smarty, &$repeat, $template)
{ 
    if (is_null($content)) {
        return;
    }
    
    if(empty($params['search'])) {
    	return $content;
    }
    if(empty($params['replace'])) {
    	$params['replace'] = '';
    }
    
    if (function_exists('mb_substr')) {
        require_once(SMARTY_PLUGINS_DIR . 'shared.mb_str_replace.php');
        return mb_str_replace($params['search'], $params['replace'], $content);
    } else {
        return str_replace($params['search'], $params['replace'], $content);
    }
}