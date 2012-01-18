<?php
/**
 * Enlight
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://enlight.de/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@shopware.de so we can send you a copy immediately.
 *
 * @category   Enlight
 * @package    Enlight_Template_Plugins
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */
/**
 * Builds an Enlight Link based on given controller and action.
 * The params array knows the following key:
 * - file     : this key must be filled with a filename
 * - fullpath : if this key is filled, a whole link will be returned http[s]://.... 
 * 
 * @param array $params
 * @param Enlight_Template_Default $template
 * @return bool|mixed|string
 */
function smarty_function_link($params, Enlight_Template_Default $template)
{
    if(empty($params['file'])) {
        return false;
    }
    $file = $params['file'];

    /** @var $front Enlight_Controller_Front */
    $front = Enlight_Application::Instance()->Front();
    $request = $front->Request();

    // check if we got an URI or a local link
    if(strpos($file, '/') !== 0 && strpos($file, '://') === false) {
        // we've got a file which looks like a link
        
        $dirs = (array) $template->smarty->getTemplateDir();
        // lets guess the webroot, if we are running inside an Enlight2 application 
        // we are able to get the right path from the configs
        if(method_exists(Enlight_Application::Instance(), 'DocPath')) {
            $docPath = Enlight_Application::Instance()->DocPath();
        } else {
            // otherwise assume we are in the webroot
            $docPath = getcwd() . DIRECTORY_SEPARATOR;
        }
        // try to find the file on the filesystem
        foreach($dirs as $dir) {
            if(file_exists($dir . $file)) {
                $file = $dir . $file;
                break;
            }
        }
        // some clean up code 
        if(strpos($file, $docPath) === 0) {
            $file = substr($file, strlen($docPath));
        }
        // make sure we have the right separator for the web context
        if(DIRECTORY_SEPARATOR !== '/') {
            $file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
        }
        // if we did not find the file, we are returning a false
        if(strpos($file, '/') !== 0) {
            if(!file_exists($docPath . $file)) {
                return false;
            }
            $file = $request->getBasePath() . '/' . $file;
        }
    }
    
    if(strpos($file, '/') === 0 && !empty($params['fullPath'])) {
        $file = $request->getScheme() . '://' . $request->getHttpHost() . $file;
    }

    return $file;
}