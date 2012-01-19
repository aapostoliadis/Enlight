<?php
class Blog_Controllers_Frontend_Search extends Enlight_Controller_Action
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
        $searchTerm = $this->Request()->searchTerm;
        $sql = "SELECT * FROM post WHERE headline like :value OR content like :value";
        $this->View()->posts = Blog()->Db()->fetchAll($sql, array("value" => "%" . $searchTerm . "%"));
        //loads the standard listing index template so we can use it for both the listing and the search
        $this->View()->loadTemplate("frontend/listing/index.tpl");
    }
}