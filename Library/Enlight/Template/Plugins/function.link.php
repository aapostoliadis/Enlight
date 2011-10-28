<?php
/**
 * Enter description here...
 *
 * @param array $params
 * @param Smarty $smarty
 * @param unknown_type $template
 * @return string
 */
function smarty_function_link($params, $smarty, $template)
{
    if(empty($params['file'])) {
        return false;
    }
    $file = $params['file'];

    if (strpos($file, '/')!==0 && strpos($file, '://')===false) {
        $dirs = (array) $smarty->getTemplateDir();
        if(method_exists(Enlight_Application::Instance(), 'DocPath')) {
            $docPath = Enlight_Application::Instance()->DocPath();
        } else {
            $docPath = getcwd() . DIRECTORY_SEPARATOR;
        }
        foreach ($dirs as $dir) {
            if(file_exists($dir.$file)) {
                $file = $dir.$file;
                break;
            }
        }
        if(strpos($file, $docPath) === 0) {
            $file = substr($file, strlen($docPath));
        }
        if(Enlight_Application::Instance()->DS() !== '/') {
            $file = str_replace(Enlight_Application::Instance()->DS(), '/', $file);
        }
        if (strpos($file, '/') !== 0) {
            if(!file_exists($docPath . $file)) {
                return false;
            }
            $request = Enlight_Application::Instance()->Front()->Request();
            $file = $request->getBasePath().'/'.$file;
        }
    }

    if (strpos($file, '/')===0 && !empty($params['fullPath'])) {
        $request = Enlight_Application::Instance()->Front()->Request();
        $file = $request->getScheme().'://'. $request->getHttpHost().$file;
    }

    return $file;
}