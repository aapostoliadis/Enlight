<?php
function smarty_modifier_currency($value, $config=null, $position=null)
{
    if(!Enlight_Application::Instance()->Bootstrap()->hasResource('Currency')) {
        return $value;
    }

    if(!empty($config) && is_string($config)) {
        $config = strtoupper($config);
        if(defined('Zend_Date::' . $config)) {
            $config = array('display' => constant('Zend_Currency::' . $config));
        } else {
            $config = array();
        }
    } else {
        $config = array();
    }

    if(!empty($position) && is_string($position)) {
        $position = strtoupper($position);
        if(defined('Zend_Date::' . $position)) {
            $config['position'] = constant('Zend_Currency::' . $position);
        }
    }

    $currency = Enlight_Application::Instance()->Currency();
    $value = floatval(str_replace(',', '.', $value));
    $value = $currency->toCurrency($value, $config);
    if(function_exists('mb_convert_encoding')) {
        $value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
    }
    $value = htmlentities($value, ENT_COMPAT, 'UTF-8', false);
    return $value;
}