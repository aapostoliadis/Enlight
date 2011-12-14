<?php
function smarty_modifier_date($value, $format = null, $type = null)
{
    if($value === 'r') {
        $value = $format;
        $format = 'r';
        $type = 'php';
    }
    if(empty($value)) {
        return '';
    }
    if(!empty($format) && is_string($format)) {
        if(defined('Zend_Date::' . strtoupper($format))) {
            $format = constant('Zend_Date::' . strtoupper($format));
        }
    }
    if(!empty($type) && is_string($type)) {
        $type = strtolower($type);
    }

    /** @var Zend_Locale $locale */
    $locale = Enlight_Application::Instance()->Bootstrap()->getResource('locale');
    if(is_string($value)) {
        $value = strtotime($value);
    }

    $date = new Zend_Date($locale, Zend_Date::TIMESTAMP, $value);
    $value = $date->toString($format, $type);

    $value = htmlentities($value, ENT_COMPAT, 'UTF-8', false);

    return $value;
}