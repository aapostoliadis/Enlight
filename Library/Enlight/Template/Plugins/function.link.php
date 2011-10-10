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
	$docPath = Enlight::Instance()->DocPath();
	
	if (strpos($file, '/')!==0 && strpos($file, '://')===false) {
		$dirs = (array) $smarty->template_dir;
		$dirs[] = $docPath;
		foreach ($dirs as $dir) {
			if(file_exists($dir.$file)) {
				$file = $dir.$file;
				break;
			}
		}
		if(strpos($file, $docPath) === 0) {
			$file = substr($file, strlen($docPath));
		}
		if(Enlight::Instance()->DS() !== '/') {
			$file = str_replace(Enlight::Instance()->DS(), '/', $file);
		}
		if (strpos($file, '/') !== 0) {
			if(!file_exists($docPath . $file)) {
				return false;
			}
			$request = Enlight::Instance()->Front()->Request();
			$file = $request->getBasePath().'/'.$file;
		}
	}
	
	if (strpos($file, '/')===0 && !empty($params['fullPath'])) {
		$request = Enlight::Instance()->Front()->Request();
		$file = $request->getScheme().'://'. $request->getHttpHost().$file;
	}
	
	return $file;
}