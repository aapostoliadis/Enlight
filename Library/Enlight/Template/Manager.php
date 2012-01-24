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
 * The Enlight_Template_Manager extends smarty so the config can be set manuel in the class constructor.
 * With the Enlight_Template_Manager it is not only possible to overwrite template files,
 * it is also possible to overwrite all the individual blocks within the template.
 *
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
     * Class constructor, initializes basic smarty properties:
     * Template, compile, plugin, cache and config directory.
     *
     * @param   null|array|Enlight_Config $options
     */
    public function __construct($options = null)
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

        $this->debug_tpl = 'file:' . SMARTY_DIR . '/debug.tpl';

        if ($options instanceof Enlight_Config) {
            $options = $options->toArray();
        }
        if ($options !== null) {
            foreach ($options as $key => $option) {
                $key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                $this->{'set' . $key}($option);
            }
        }
    }

    /**
     * Creates a template object
     *
     * @param string  $template the resource handle of the template file
     * @param mixed   $cache_id cache id to be used with this template
     * @param mixed   $compile_id compile id to be used with this template
     * @param object  $parent next higher level of Smarty variables
     * @param boolean $do_clone flag is Smarty object shall be cloned
     * @return object template object
     */
    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = false)
    {
        return parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone);
    }
}