<?php
class Blog_Controllers_Frontend_Listing extends Enlight_Controller_Action
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
        $sql = "SELECT * FROM post ORDER BY creation_date DESC ";
        $posts = Blog()->Db()->fetchAll($sql);
        $this->View()->posts = $posts;
        $breadcrumbs = array(array("action" => "index","controller" => "listing", "name" => "All Posts"));
        $this->View()->breadcrumbs = $breadcrumbs;
        $this->View()->activeMenu = $this->Request()->getControllerName();
    }
}