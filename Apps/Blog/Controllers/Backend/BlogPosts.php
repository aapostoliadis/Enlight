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
 * Blog Backend Controller
 * allocates the blog posts data to the backend view
 *
 * @category   Enlight
 * @package    Blog
 * @subpackage Blog_Controllers
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Blog_Controllers_Backend_BlogPosts extends Enlight_Controller_Action
{

    /**
     * Init method will automatically be called when the Controller instance is created
     *
     * Decodes the json request params into a standard Web Request so you can access them by default like this:
     *
     * <code>$this->Request()->...</code>
     */
    public function init()
    {
        $this->Front()->Plugins()->JsonRequest()
                ->setParseInput()
                ->setParseParams(array('group', 'sort'));
        $this->Front()->Plugins()->ScriptRenderer()->setRender();
    }

    /**
     * Will automatically be called before any other action.
     *
     * Prevents to render any template by the given action name,
     * because we need only the json encoded data.
     */
    public function preDispatch()
    {
        if (in_array($this->Request()->getActionName(), array('getPosts', 'createPost', 'deletePost', 'updatePost'))) {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        }
    }

    /**
     * empty needed action method
     */
    public function indexAction()
    {
    }

    /**
     * empty needed action method
     */
    public function loadAction()
    {
    }


    /**
     * returns all posts ordered by the creation_date
     *
     * @return JsonData | all blog post data
     */
    public function getPostsAction()
    {
        $sql = "SELECT * FROM post ORDER BY creation_date DESC ";
        $posts = Blog()->Db()->fetchAll($sql);
        echo Zend_Json::encode(array('success' => true, 'data' => $posts));
    }

    /**
     * insert the new blog post data into the db table based on the request data
     *
     * @return JsonData | the created blog post
     */
    public function createPostAction()
    {

        $headline = $this->Request()->headline;
        $content = $this->Request()->content;
        $creation_date = $this->Request()->creation_date;
        $sql = "INSERT INTO `post` (`id`, `headline`, `content`, `creation_date`) VALUES (NULL, ?, ?, ?);";
        Blog()->Db()->query($sql, array($headline, $content, $creation_date));
        //return the inserted post to the grid
        $newPost = array();
        $newPost["id"] = Blog()->Db()->lastInsertId();
        $newPost["headline"] = $headline;
        $newPost["content"] = $content;
        $newPost["creation_date"] = $creation_date;

        echo Zend_Json::encode(array('success' => true, 'data' => $newPost));
    }

    /**
     * updates the blog post based on the request data
     *
     * @return JsonData | the updated blog post
     */
    public function updatePostAction()
    {
        $id = $this->Request()->id;
        $headline = $this->Request()->headline;
        $content = $this->Request()->content;
        $creation_date = $this->Request()->creation_date;

        $sql = "UPDATE `post`
                SET `headline` = ?,
                `content` = ?,
                `creation_date` = ?
                WHERE `id` =?";
        Blog()->Db()->query($sql, array($headline,$content,$creation_date,$id));

        //return the inserted post to the grid
        $newPost = array();
        $newPost["id"] = $id;
        $newPost["headline"] = $headline;
        $newPost["content"] = $content;
        $newPost["creation_date"] = $creation_date;
        echo Zend_Json::encode(array('success' => true, 'data' => $newPost));
    }

    /**
     * updates the blog post based on the request data
     * @return JsonData  success message
     */
    public function deletePostAction()
    {
        $postID = $this->Request()->id;
        $sql = "DELETE FROM `post` WHERE `id` = ?";
        Blog()->Db()->query($sql, $postID);
        echo Zend_Json::encode(array('success' => true));
    }
}