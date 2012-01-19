<?php
class Blog_Controllers_Backend_BlogPosts extends Enlight_Controller_Action
{

    public function init()
    {
        /**
         * @deprecated remove it when the adapter class is done
         */
        $this->Front()->Plugins()->ScriptRenderer()->setRender();
        // Prepare incoming data
        if ($this->Request()->isPost() && !count($this->Request()->getPost())) {
            $data = file_get_contents('php://input');
            $data = Zend_Json::decode($data);
            // Remove null values because Zend_Request may crash
            foreach ((array)$data as $key => $value) {
                if ($value !== null) {
                    $this->Request()->setPost($key, $value);
                }
            }
        }
    }

    public function preDispatch()
    {
        $this->View()->setCaching(true);

        if (in_array($this->Request()->getActionName(), array('getPosts', 'createPost', 'deletePost', 'updatePost'))) {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        }
    }

    //empty methods to
    public function indexAction()
    {
    }

    public function loadAction()
    {
    }


    /**
     *
     *
     * @return void
     */
    public function getPostsAction()
    {
        $sql = "SELECT * FROM post ORDER BY creation_date DESC ";
        $posts = Blog()->Db()->fetchAll($sql);
        echo Zend_Json::encode(array('success' => !empty($posts), 'data' => $posts));
    }

    /**
     *
     *
     * @return void
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
        $newPost["id"] = Blog()->Db->lastInsertId();
        $newPost["headline"] = $headline;
        $newPost["content"] = $content;
        $newPost["creation_date"] = $creation_date;
        echo Zend_Json::encode(array('success' => true, 'data' => $newPost));
    }

    /**
     *
     *
     * @return void
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
     *
     * @return void
     */
    public function deletePostAction()
    {
        $postID = $this->Request()->id;
        $sql = "DELETE FROM `post` WHERE `id` = ?";
        Blog()->Db()->query($sql, $postID);
        echo Zend_Json::encode(array('success' => true));
    }
}