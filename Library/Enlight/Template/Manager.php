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
class Enlight_Template_Manager extends Smarty
{
    /**
     * The name of class used for templates
     *
     * @var string
     */
    public $template_class = 'Enlight_Template_Default';

    /**
     * Class constructor, initializes basic smarty properties
     */
    public function __construct()
    {
    	// self pointer needed by some other class methods
        $this->smarty = $this;
        $this->start_time = microtime(true);
        
        // set default dirs
        $this->setTemplateDir('.' . DS . 'templates' . DS)
            ->setCompileDir('.' . DS . 'templates_c' . DS)
            ->setPluginsDir(array(dirname(__FILE__) . '/Plugins/', SMARTY_PLUGINS_DIR))
            ->setCacheDir('.' . DS . 'cache' . DS)
            ->setConfigDir('.' . DS . 'configs' . DS);

        $this->debug_tpl = 'file:' . dirname(__FILE__) . '/debug.tpl';
    }

    /**
    * Creates a template object
    *
    * @param string $template the resource handle of the template file
    * @param mixed $cache_id cache id to be used with this template
    * @param mixed $compile_id compile id to be used with this template
    * @param object $parent next higher level of Smarty variables
    * @param boolean $do_clone flag is Smarty object shall be cloned
    * @return object template object
    */
    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = false)
    {
        return parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone);
    }
}