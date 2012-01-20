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
 * @package    Blog
 * @subpackage Blog_Controllers
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 * @version    $Id$
 * @author     Heiner Lohaus
 * @author     Marcel Schmaeing
 * @author     $Author$
 */

/**
 * Blog Frontend Search Controller
 * Search all needed data for the listing based on the searchTerm
 *
 * Don't need an own template. It is using the listing template
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Controllers
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Controllers_Frontend_Search extends Enlight_Controller_Action
{

    /**
     * pre Dispatch method
     * Will be executed before any other action is called.
     */
    public function preDispatch()
    {
        /**
         * Loads the listing template so the template can be used for the listing and the search action.
         * The template has to be loaded at first, if you load the template after setting the smarty variables,
         * the Enlight_View_Default class will reload the smarty variables and reset your values.
         */
        $this->View()->loadTemplate("frontend/listing/index.tpl");
    }

    /**
     * Search for blog posts based on the requested searchTerm
     */
    public function indexAction()
    {

        //save Request variable searchTerm
        $searchTerm = $this->Request()->searchTerm;
        $sql = "SELECT * FROM post WHERE headline like :value OR content like :value";
        $this->View()->posts = Blog()->Db()->fetchAll($sql, array("value" => "%" . $searchTerm . "%"));
        //just to highlight the current menu
        $this->View()->activeMenu = $this->Request()->getControllerName();
    }
}