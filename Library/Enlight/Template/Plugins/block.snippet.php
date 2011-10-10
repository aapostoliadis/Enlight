<?php
function smarty_block_snippet($params, $content, $smarty, &$repeat, $template)
{ 
	if($content===null) {
		return;
	}
	
	if(empty($content)) {
		$content = '#'.$params['name'].'#';
	}
	
	if(!empty($params['class'])) {
		$params['class'] .= ' '.str_replace('/', '_', $params['namespace']);
	} else {
		$params['class'] = str_replace('/', '_', $params['namespace']);
	}
	
	if(!empty($params['tag'])) {
		if(!empty($params['tag'])) {
			$params['tag'] = strtolower($params['tag']);
		} else {
			$params['tag'] = 'span';
		}
			
		if(!empty($params['class'])) {
			$params['class'] .= ' shopware_studio_snippet';
		} else {
			$params['class'] = 'shopware_studio_snippet';
		}
			
		$attr = '';
		foreach ($params as $key => $param) {
			if(in_array($key, array('name', 'tag', 'assign', 'name', 'namespace', 'default', 'force'))) {
				continue;
			}
			$attr .= ' '.$key.'="'.htmlentities($param, ENT_COMPAT, mb_internal_encoding(), false).'"';
		}
		
		$content = htmlentities($content, ENT_COMPAT, mb_internal_encoding(), false);
		$content = "<{$params['tag']}$attr>".$content."</{$params['tag']}>";
	}
			
    if (isset($params['assign'])) {
       $template->assign($params['assign'], $content);
    } else {
       return $content;
    }
}