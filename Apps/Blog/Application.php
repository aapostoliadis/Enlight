<?php
include_once 'Enlight/Application.php';

class Blog_Application extends Enlight_Application
{
    public function Db()
    {
        return $this->Bootstrap()->getResource('Db');
    }
}

/**
 * @return Blog_Application
 */
function Blog()
{
    return Blog_Application::Instance();
}
