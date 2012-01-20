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
 * Blog Frontend Detail Controller
 * Creating and getting all needed data for the blog post detail page
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Controllers
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Controllers_Frontend_Detail extends Enlight_Controller_Action
{

    /**
     * Will be called by default if no other action is called.
     * Reads the blog post with the requested postID from the db and
     * reads different three blog posts to teaser them
     */
    public function indexAction()
    {
        //save Request variable postID
        $postId = intval($this->Request()->postID);


        $sql = "SELECT * FROM post WHERE id = ?";
        $post = Blog()->Db()->fetchRow($sql, array($postId));

        //allocates all posts to the view so you can access them via smarty
        $this->View()->post = $post;

        $sql = "SELECT * FROM post WHERE id != ? ORDER by creation_date LIMIT 3 ";
        $posts = Blog()->Db()->fetchAll($sql, array($postId));
        $this->View()->teaserPosts = $posts;
    }
}