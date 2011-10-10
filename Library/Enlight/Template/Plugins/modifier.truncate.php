<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 
/**
 * Smarty truncate modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *               optionally splitting in the middle of a word, and
 *               appending the $etc string or inserting $etc into the middle.
 * 
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com> 
 * @param string $string input string
 * @param integer $length lenght of truncated text
 * @param string $etc end string
 * @param boolean $break_words truncate at word boundary
 * @param boolean $middle truncate in the middle of text
 * @return string truncated string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '...',
    $break_words = false, $middle = false)
{
    if ($length == 0)
        return '';

    if (is_callable('mb_strlen')) {
    	$string = mb_convert_encoding($string, 'UTF-8', 'HTML-ENTITIES');
        if (mb_strlen($string) > $length) {
            $length -= min($length, mb_strlen($etc, 'UTF-8'));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1, 'UTF-8'));
            } 
            if (!$middle) {
                $string = mb_substr($string, 0, $length, 'UTF-8') . $etc;
            } else {
                $string = mb_substr($string, 0, $length / 2, 'UTF-8') . $etc . mb_substr($string, - $length / 2, 'UTF-8');
            } 
        }
        if(mb_internal_encoding()!='UTF-8') {
        	$string = mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8');
        	$string = html_entity_decode($string, null, mb_internal_encoding());
        }
        return $string;
    }
    // $string has no utf-8 encoding
    if (strlen($string) > $length) {
        $length -= min($length, strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
        } 
        if (!$middle) {
            return substr($string, 0, $length) . $etc;
        } else {
            return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
        } 
    } else {
        return $string;
    } 
}