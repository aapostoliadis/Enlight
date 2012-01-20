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
 * Blog Frontend Listing Controller
 * Creating and getting all needed data for the listings
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Controllers
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Controllers_Frontend_Listing extends Enlight_Controller_Action
{

    /**
     * Will be called by default if no other action is called.
     * Reads all blog posts order by creation date
     */
    public function indexAction()
    {
        $sql = "SELECT * FROM post ORDER BY creation_date DESC ";
        $posts = Blog()->Db()->fetchAll($sql);

        //allocates all posts to the view so you can access them via smarty
        $this->View()->posts = $posts;

        //just to highlight the current menu
        $this->View()->activeMenu = $this->Request()->getControllerName();
    }
}