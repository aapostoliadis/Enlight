<?php
function smarty_block_replace($params, $content, $smarty, &$repeat, $template)
{ 
    if (is_null($content)) {
        return;
    }
    if (!function_exists('mb_str_replace')) {
        // simulate the missing PHP mb_str_replace function
        function mb_str_replace($needles, $replacements, $haystack)
        {
            $rep = (array)$replacements;
            foreach ((array)$needles as $key => $needle) {
                $replacement = $rep[$key];
                $needle_len = mb_strlen($needle);
                $replacement_len = mb_strlen($replacement);
                $pos = mb_strpos($haystack, $needle, 0);
                while ($pos !== false) {
                    $haystack = mb_substr($haystack, 0, $pos) . $replacement
                     . mb_substr($haystack, $pos + $needle_len);
                    $pos = mb_strpos($haystack, $needle, $pos + $replacement_len);
                }
            }
            return $haystack;
        }
    }
    
    if(empty($params['search'])) {
    	return $content;
    }
    if(empty($params['replace'])) {
    	$params['replace'] = '';
    }
    
    if (function_exists('mb_substr')) {
        return mb_str_replace($params['search'], $params['replace'], $content);
    } else {
        return str_replace($params['search'], $params['replace'], $content);
    }
}