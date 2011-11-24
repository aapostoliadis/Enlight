<?php
function smarty_modifiercompiler_template_exists($params, $compiler)
{
    return '$_smarty_tpl->smarty->templateExists('. $params[0] .')';
}