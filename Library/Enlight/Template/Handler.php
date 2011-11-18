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
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     $Author$
 */

require_once('Smarty/Smarty.class.php');

/**
 * @category   Enlight
 * @package    Enlight_Template
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Template_Handler extends Smarty_Internal_Template
{
    /**
     * @param $spec
     * @param $content
     * @param string $mode
     * @return void
     */
    public function extendsBlock($spec, $content, $mode = 'replace')
    {
    	if (strpos($content, $this->smarty->left_delimiter . '$smarty.block.child' . $this->smarty->right_delimiter) !== false) {
    		if (isset($this->block_data[$spec])) {
    			$content = str_replace(
                    $this->smarty->left_delimiter . '$smarty.block.child' . $this->smarty->right_delimiter,
                    $this->block_data[$spec]['source'],
                    $content
                );
    			unset($this->block_data[$spec]);
    		} else {
    			$content = str_replace($this->smarty->left_delimiter.'$smarty.block.child'.$this->smarty->right_delimiter, '', $content);
    		}
    	}
    	if (isset($this->block_data[$spec])) {
    		if (strpos($this->block_data[$spec]['source'], '%%%%SMARTY_PARENT%%%%') !== false) {
    			$content = str_replace('%%%%SMARTY_PARENT%%%%', $content, $this->block_data[$spec]['source']);
    		} elseif ($this->block_data[$spec]['mode'] == 'prepend') {
    			$content = $this->block_data[$spec]['source'] . $content;
    		} elseif ($this->block_data[$spec]['mode'] == 'append') {
    			$content .= $this->block_data[$spec]['source'];
    		}
    	}
    	$this->block_data[$spec] = array('source'=>$content, 'mode'=>$mode, 'file'=>null);
    }

    /**
     * @param $template_name
     * @return void
     */
	public function extendsTemplate($template_name)
    {
        //if(strpos($this->template_resource, 'extends:') !== 0) {
        //    $this->template_resource = 'extends:' . $this->template_resource;
        //}
    	$this->template_resource .= '|' . $template_name;
    }

    /*
    public function renderTemplate()
    {
    	$obLevel = ob_get_level();
    	try {
    		return parent::renderTemplate();
    	} catch (Exception $e) {
			while (ob_get_level() > $obLevel) {
				ob_get_clean();
			}
    		throw $e;
    	}
    }
    */
}