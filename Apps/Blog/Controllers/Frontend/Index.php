<?php
class Blog_Controllers_Frontend_Index extends Enlight_Controller_Action
{
    public function preDispatch()
    {
        $this->View()->setCaching(false);
        $this->View()->Template()->cache_lifetime = 1000;
        $this->View()->setScope(Smarty::SCOPE_GLOBAL);

        //sets the current controller name to the view
        $this->View()->controller = $this->controller_name;
    }

    /**
     * reads the first three blog entries
     */
    public function indexAction()
    {
        $sql = "SELECT * FROM post ORDER by creation_date DESC LIMIT 3";
        $posts = Blog()->Db()->fetchAll($sql);
        $this->View()->teaserPosts = $posts;

        //just to highlight the menu
        $this->View()->activeMenu = $this->Request()->getControllerName();
    }

    public function menuAction()
    {
        if (!$this->View()->isCached()) {
            $this->View()->Site = Enlight_Application::Instance()->Site();
        }
    }
}