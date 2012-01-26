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
 * Build an link based on given controller and action name
 *
 * Parameters known by $params
 * - module     : name of the module
 * - controller : name of the controller
 * - action     : name of the action
 */
class Smarty_Compiler_Url extends Smarty_Internal_CompileBase
{

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Array of names of valid option flags
     *
     * @var array
     */
    public $option_flags = array('appendSession', 'forceSecure', 'forceSecure');


    /**
     * @param $args
     * @param $compiler
     * @return string
     */
    public function compile($args, $compiler)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        // default 'false' for all option flags not set
        foreach($_attr as $index => $param) {
            if ($param === false) {
                unset($_attr[$index]);
            } elseif ($param === true) {
                $_attr[$index] = "'1'";;
            }
        }

        $params = array();
        foreach($_attr as $index => $param) {
            if (!preg_match('/^([\'"]?)[a-zA-Z0-9]+(\\1)$/', $param, $match) || !empty($_attr['appendSession'])) {
                return '<?php echo Enlight_Application::Instance()->Front()->Router()->assemble(' . join( ', ', $_attr ) . '); ?>';
            }
            $params[$index] = substr($param, 1, -1);
        }

        $url = Enlight_Application::Instance()->Front()->Router()->assemble($params);

        return '<?php echo ' . var_export($url, true) . ';?>';
    }
}