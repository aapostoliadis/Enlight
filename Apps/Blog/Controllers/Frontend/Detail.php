<?php
class Blog_Controllers_Frontend_Detail extends Enlight_Controller_Action
{
    public function preDispatch()
    {
        $this->View()->setCaching(false);
        $this->View()->Template()->cache_lifetime = 1000;
        $this->View()->setScope(Smarty::SCOPE_GLOBAL);
    }

    /**
     * reads the first three blog entries
     */
    public function indexAction()
    {
        $postId = intval($this->Request()->postID);
        $sql = "SELECT * FROM post WHERE id = ?";
        $post = Blog()->Db()->fetchRow($sql, array($postId));

        $this->View()->post = $post;
        $breadcrumbs = array(array("action" => "index","controller" => "detail", "name" => $post["headline"]));

        $this->View()->breadcrumbs = $breadcrumbs;
        $sql = "SELECT * FROM post WHERE id != ? ORDER by creation_date LIMIT 3 ";
        $posts = Blog()->Db()->fetchAll($sql, array($postId));
        $this->View()->teaserPosts = $posts;
    }
}